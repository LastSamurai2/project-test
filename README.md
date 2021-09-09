## Setup

1. Fetch the repo, cd to the newly created directory

```bash
git clone ssh://git@git.alekseon.com:2222/alekseon/adminapp.git
cd adminapp
```

2. Download .env file from Alekseon's disk
- You need to obtain .env, in order to do that please issue following command (ssh password for disk can be found here: https://app.getguru.com/card/Ty6R5aGc/Local-Disk-on-Router):
```bash
scp -P 2020 lubieplacki@192.168.1.1:/mnt/sda1/dyskalekseon/bizami.com/.env .
```

3. Add domain to Your /etc/hosts file
```bash
127.0.0.1   bizami.local
```

## Development

1. Build project
```bash
make build
```

2. Fire up the project
```bash
make up
```

3. In order to stop all docker services please issue this command
```bash
make down
```

## Troubleshooting
1. If you have port 9003 busy, you have to disable PHPStorm temporarily

## Services

- You will find mailhog @ http://bizami.local:8025
