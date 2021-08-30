# Crayta Stub Generator

Lua stub generator for the Crayta API Docs.

Crayta is a Free Game Creation Game. Go check it out here:
(https://www.crayta.com)

## Requirements:

- PHP 7.0 or higher (https://www.php.net/manual/en/install.php)
- Composer (https://getcomposer.org/download/)
- Legendary (https://github.com/derrod/legendary)

## Installation

Add this package to your project by running
`composer require yogarine/crayta-stub-generator`
or install it globally using
`composer global require yogarine/crayta-stub-generator`.

## Updating the LuaDocs xml files (using Legendary)

1. Install and configure Legendary
   (see https://github.com/derrod/legendary#how-to-runinstall)
2. Run `legendary list-games` to get the proper `app name`:
   ```bash
   legendary list-games
   ```
   The `app name` might be a 32 character hash like
   `a0a49d82e3f64c1b81873397a6e92f09`.
3. Install the app with the given app name:
   ```bash
   legendary install <app name>
   ```
4. Copy over de LuaDocs.
   ```bash
   cp -rf ~/legendary/Crayta/Crayta/Content/LuaDocs/* LuaDocs/
   ```

## Usage

Run `vendor/bin/create-crayta-stubs` from your project dir,
or `create-crayta-stubs` if installed globally, to generate the stubs. They will
be placed in the `stubs` subdirectory of the current working directory.

## Disclaimer

This project isn't officially affiliated with Crayta, nor it's developer, Unit 2
Games, in any way. Crayta is a registered trademark and the Crayta stubs and the
documentation it includes are Copyrighted by Unit 2 Games. The stubs may be
distributed for the promotion of Crayta only, as per Unit 2 Games' Terms of
Service.
