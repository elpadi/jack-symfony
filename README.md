# The Jack Mag

- [Homepage](https://thejackmag.com)
- [Staging](https://staging.thejackmag.com)
- [Symfony 5.4](https://symfony.com/doc/5.4/index.html)

## Admin

- [Cockpit](https://cockpit.thejackmag.com/)
- [Docs](https://github.com/agentejo/cockpit)

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
