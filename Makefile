.PHONY: test lint
test:
	vendor/bin/phpunit -c phpunit.xml.dist
lint:
	vendor/bin/phpcs
