.PHONY: all dev ci ci-install install build watch start stop clean

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

start:
	pnpm run env:start

stop:
	pnpm run env:stop

clean:
	rm -rf node_modules
	rm -rf build
