<?php

$container->set(
	HelloAsso::class,
	static function (ContainerInterface $container) {
		$helloasso = new HelloAsso($container->get('zdb'));
		$helloasso->load();
		if($helloasso->refreshTokenExpiresSoon()) {
			$helloasso->refreshTokens();
		}
		return $helloasso;
	},
);
