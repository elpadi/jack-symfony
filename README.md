# The Jack Mag

- [Homepage](https://thejackmag.com)
- [Staging](https://staging.thejackmag.com)
- [Symfony 5.4](https://symfony.com/doc/5.4/index.html)

## Admin

- [Cockpit](https://cockpit.thejackmag.com/)
- [Docs](https://github.com/agentejo/cockpit)

### Adding a New Page

- Add an entry in the [Pages collection](https://cockpit.thejackmag.com/collections/entries/pages).
- Add a [custom route](https://github.com/elpadi/jack-symfony/blob/peep-couture/src/Controller/FrontendController.php) only when custom data is needed, i.e. Models or overrides.
- Add [custom css](https://github.com/elpadi/jack-symfony/blob/peep-couture/assets/css/app.css) if needed.
- Add a [submenu entry](https://cockpit.thejackmag.com/collections/entries/mainsubnavs).

## Deploying Updates

```sh
ENV={staging|prod} ./bin/deploy.sh [public] [symfony]
```

## Updating Front-End Assets

### JS Analysis

`lando yarn run eslint assets/js`

### Build & Deploy

1. `npm run build`
2. `./bin/deploy.sh public`
