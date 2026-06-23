<?php

declare(strict_types=1);

namespace Maiscraft\CryptoHyperf;

use Hyperf\Contract\ConfigInterface;
use Maiscraft\Crypto\CryptoManager;
use Psr\Container\ContainerInterface;

/**
 * Hyperf DI 配置提供者
 * 只做配置读取 + CryptoManager 注册 + Command 注册
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                CryptoManager::class => static function (ContainerInterface $container) {
                    $config = $container->get(ConfigInterface::class);
                    $cryptoConfig = $config->get('crypto', []);

                    return new CryptoManager($container, $cryptoConfig);
                },
            ],
            'commands' => [
                GenerateKeyCommand::class,
            ],
            'publish' => [
                [
                    'id' => 'crypto-config',
                    'description' => 'Crypto configuration file.',
                    'source' => __DIR__ . '/../publish/crypto.php',
                    'destination' => BASE_PATH . '/config/autoload/crypto.php',
                ],
            ],
        ];
    }
}
