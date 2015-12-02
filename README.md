# imbo-multi-backend

Use multiple database/storage backends for Imbo

## Installation

### Setting up the dependencies

If you've installed Imbo through composer, getting the multi backend adapter up and running is really simple. Simply add `imbo/imbo-multi-backend` as a dependency and run `composer update`.

```json
{
    "require": {
        "imbo/imbo-multi-backend": "dev-master",
    }
}
```

### Configuring Imbo

Once you've got it installed, you need to configure the adapter. An example configuration file can be found in `config/config.dist.php`. Simply copy the file to your Imbo `config` folder, adjust the parameters and name it `multi-backend.php`, for instance. Imbo should pick it up automatically and use the configured adapters.

## A word of warning

This is in early stages of development, and there are a lot of scenarios that have not been accounted for. The way it works currently is to simply loop through the backends provided and try to perform the same operation on each backend, one at a time. Exceptions triggered from any of these adapters can cause the two backends to end up in an unsynced state. In the future, you could image some sort of rollback pattern being applied in cases like this, to try and have synchronized backends.

For fetch operations, it'll try to read the data/status from each backend until it finds one that returns a positive result. If none is found, a 404 is triggered (as expected).

# License

Copyright (c) 2015, [Espen Hovlandsdal](mailto:espen@hovlandsdal.com)

Licensed under the MIT License
