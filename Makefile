.PHONY: build-image
build-image:
	docker build . -t howtotest-php

.PHONY: bash
bash:
	docker run --rm -it -v $(PWD):/app -u www-data howtotest-php bash

.PHONY: install-dependencies
install-dependencies:
	docker run --rm -it -v $(PWD):/app -u www-data howtotest-php composer install

.PHONY: test
test:
	docker run --rm -v $(PWD):/app -u www-data howtotest-php php ./vendor/bin/phpunit
