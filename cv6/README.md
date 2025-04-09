Ištalácia programu `supervisor`:

```sh
$ sudo apt update && sudo apt install supervisor
```

Adresár pre konfiguračné súbory: `/etc/supervisor/conf.d/`

Základné príkazy:
```sh
$ sudo supervisorctl reread # načítanie .conf súborov
$ sudo supervisorctl update # aktualizácia taskov
$ sudo supervisorctl status # stav taskov
$ sudo supervisor stop <task> # zastavenie tasku <task>
$ sudo supervisor restart <task> # reštart tasku <task>
$ sudo supervisor stop all # zastavenie všetkých taskov
$ sudo supervisor restart all # reštart všetkých taskov
```