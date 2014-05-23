test:
	./vendor/bin/phpunit

install:
	./composer.phar install

clean:
	rm -rf ./vendor
