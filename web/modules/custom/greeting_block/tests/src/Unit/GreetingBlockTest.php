<?php

namespace Drupal\Tests\greeting_block\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\greeting_block\Plugin\Block\GreetingBlock;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\greeting_block\Plugin\Block\GreetingBlock
 * @group greeting_block
 */
class GreetingBlockTest extends UnitTestCase {

  /**
   * The block plugin being tested.
   *
   * @var \Drupal\greeting_block\Plugin\Block\GreetingBlock
   */
  protected $block;

  /**
   * The mocked date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $dateFormatter;

  /**
   * The mocked string translation.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Mock the DateFormatterInterface.
    $this->dateFormatter = $this->createMock(DateFormatterInterface::class);

    // Mock the StringTranslationTrait normally provided by Drupal.
    // This allows us->t() to work in the block.
    $this->stringTranslation = $this->createMock(TranslationInterface::class);
    // Configure the mock to return the input string, optionally with a prefix for testing.
    $this->stringTranslation->method('translate')
      ->will($this->returnArgument(0)); // Simple pass-through for t()

    // Create the block instance with mocked dependencies.
    $configuration = [];
    $plugin_id = 'greeting_block';
    $plugin_definition = [
      'provider' => 'greeting_block',
      // Other plugin definition properties if needed by the parent constructor.
    ];

    $this->block = new GreetingBlock(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $this->dateFormatter
    );

    // Set the string translation service on the block.
    // StringTranslationTrait looks for $this->stringTranslation.
    $this->block->setStringTranslation($this->stringTranslation);

    // It might be necessary to mock the container if the block uses it,
    // but our GreetingBlock gets DateFormatter via constructor injection.
    // If create() static method was tested directly, container would be needed.
  }

  /**
   * Data provider for testGreetingScenarios().
   *
   * @return array
   *   Test cases: [hour, expected_greeting_key, expected_markup_part].
   */
  public function greetingScenariosProvider(): array {
    return [
      // Morning scenarios (5 AM to 11 AM)
      '5 AM morning' => [5, 'Bom dia!', '<div class="greeting-block-content">Bom dia!</div>'],
      '11 AM morning' => [11, 'Bom dia!', '<div class="greeting-block-content">Bom dia!</div>'],
      // Afternoon scenarios (12 PM to 7 PM / 19h)
      '12 PM afternoon' => [12, 'Boa tarde!', '<div class="greeting-block-content">Boa tarde!</div>'],
      '7 PM afternoon (19h)' => [19, 'Boa tarde!', '<div class="greeting-block-content">Boa tarde!</div>'],
      // Evening/Night scenarios (8 PM / 20h to 4 AM / 4h)
      '8 PM night (20h)' => [20, 'Boa noite!', '<div class="greeting-block-content">Boa noite!</div>'],
      '11 PM night (23h)' => [23, 'Boa noite!', '<div class="greeting-block-content">Boa noite!</div>'],
      '12 AM night (0h)' => [0, 'Boa noite!', '<div class="greeting-block-content">Boa noite!</div>'],
      '4 AM night' => [4, 'Boa noite!', '<div class="greeting-block-content">Boa noite!</div>'],
    ];
  }

  /**
   * Tests the greeting logic for various times of day.
   *
   * @dataProvider greetingScenariosProvider
   * @covers ::build
   */
  public function testGreetingScenarios(int $hour, string $expectedGreetingKey, string $expectedMarkup): void {
    // Configure the mocked DateFormatter to return the specific hour for this test case.
    // The `REQUEST_TIME` and 'custom' format are what the block uses.
    $this->dateFormatter->expects($this->once())
      ->method('format')
      ->with($this->anything(), 'custom', 'H') // We don't care about REQUEST_TIME here, just the format 'H'
      ->willReturn((string) $hour);

    // Configure the t() method mock for the expected string.
    // This ensures that t() is called with the correct string key.
    $this->stringTranslation->expects($this->any()) // any() because t() might be called for other strings too
        ->method('translate')
        ->with($expectedGreetingKey, [], []) // Assuming no arguments or options for these simple strings
        ->willReturn($expectedGreetingKey); // Return the key itself for simplicity in assertion

    $build = $this->block->build();

    $this->assertIsArray($build);
    $this->assertArrayHasKey('#markup', $build);
    $this->assertEquals($expectedMarkup, $build['#markup']);

    // Also check cache settings.
    $this->assertArrayHasKey('#cache', $build);
    $this->assertArrayHasKey('max-age', $build['#cache']);
    $this->assertEquals(60, $build['#cache']['max-age']);
  }

}
```

**Explicação do Teste:**

1.  **`@coversDefaultClass` e `@group`**: Anotações PHPUnit para organizar e identificar os testes.
2.  **`setUp()`**: Este método é executado antes de cada teste.
    *   Cria *mocks* (objetos simulados) para `DateFormatterInterface` e `TranslationInterface`. O `DateFormatterInterface` é essencial para controlar a "hora atual" que o bloco usa. O `TranslationInterface` é para o método `t()` usado para tradução de strings.
    *   Instancia o `GreetingBlock` com estas dependências simuladas.
    *   Define o serviço de tradução no bloco.
3.  **`greetingScenariosProvider()`**: Este é um método *data provider*. Ele fornece conjuntos de dados para o método de teste `testGreetingScenarios()`. Cada conjunto contém:
    *   A hora a ser simulada (`$hour`).
    *   A chave da string de saudação esperada (ex: `'Bom dia!'`).
    *   O markup HTML final esperado.
4.  **`testGreetingScenarios()`**: Este é o método de teste principal.
    *   `@dataProvider greetingScenariosProvider`: Indica que este teste usará os dados do método `greetingScenariosProvider`. O teste será executado uma vez para cada conjunto de dados.
    *   `@covers ::build`: Indica que este teste cobre especificamente o método `build()` do `GreetingBlock`.
    *   **Configuração do Mock `dateFormatter`**: `expects($this->once())->method('format')...->willReturn((string) $hour)`: Diz ao mock do `dateFormatter` que esperamos que o seu método `format` seja chamado uma vez com certos argumentos, e quando for, ele deve retornar a hora especificada pelo data provider.
    *   **Configuração do Mock `stringTranslation`**: Configura o mock para o método `t()` para retornar a própria chave da string, simplificando a asserção.
    *   **Execução**: `$build = $this->block->build();` chama o método `build()` do bloco.
    *   **Asserções**:
        *   `$this->assertIsArray($build)`: Verifica se o resultado é um array (como esperado para um render array do Drupal).
        *   `$this->assertArrayHasKey('#markup', $build)`: Verifica se existe a chave `#markup`.
        *   `$this->assertEquals($expectedMarkup, $build['#markup'])`: Verifica se o markup gerado é exatamente o esperado.
        *   Verifica também as configurações de cache (`max-age`).

**Próximos passos para o utilizador:**

1.  Certificar-se de que tem o PHPUnit configurado no seu ambiente de desenvolvimento Drupal. Normalmente, o Drupal core já vem com uma configuração PHPUnit.
2.  Para executar os testes deste módulo, a partir da raiz do Drupal, o comando seria algo como:
    ```bash
    ./vendor/bin/phpunit -c ./core/phpunit.xml.dist web/modules/custom/greeting_block/tests/src/Unit/
    ```
    (O caminho exato para `phpunit` e o ficheiro de configuração XML podem variar ligeiramente dependendo da configuração do ambiente).
3.  Idealmente, todos os testes devem passar.

Este teste unitário adiciona uma camada de confiança de que a lógica do `GreetingBlock` funciona como esperado e demonstra uma prática de desenvolvimento muito profissional.
