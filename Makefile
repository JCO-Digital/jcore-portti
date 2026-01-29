.PHONY: all dev ci ci-install install build watch clean

all: install build

dev: install watch

ci: install build

ci-install: install

install:
	pnpm i

build:
	pnpm build

watch:
	pnpm run watch

clean:
	rm -rf node_modules
	rm -rf build
