@servers(['preprod' => 'user@94.23.9.161'])

@setup
    $repo = 'ssh://git@git.alekseon.com:2222/alekseon/adminapp.git';
    if ($env === 'prod') {
        $appDir = '/var/www/html/adminapp.alekseon-test.eu';
        $branch = 'master';
    } else {
        $appDir = '/var/www/html/adminapp.alekseon-test.eu';
        $branch = 'rtm';
        $fpmContainerName = "$(docker ps -a | grep k8s_fpm.*_alekseon-preprod-adminapp.* | awk '{print $1;}')";
    }

    date_default_timezone_set('Europe/Warsaw');
    $date = date('YmdHis');

    $builds = $appDir . '/sources';
    $deployment = $builds . '/' . $date;
    $serve = $appDir . '/source';
    $env = $appDir . '/.env';
    $storage = $appDir . '/storage';
@endsetup

@story('deploy')
    git
    install
    live
@endstory

@task('git')
    git clone -b {{ $branch }} "{{ $repo }}" {{ $deployment }}
@endtask

@task('install')
    docker exec {{ $fpmContainerName }} bash -c 'rm {{ $storage }}/app -rf'
    docker exec {{ $fpmContainerName }} bash -c 'cd {{ $deployment }} && mv {{ $deployment }}/storage/app {{ $storage }}'
    docker exec {{ $fpmContainerName }} bash -c 'cd {{ $deployment }} && rm -rf {{ $deployment }}/storage'
    docker exec {{ $fpmContainerName }} bash -c 'cd {{ $deployment }} && ln -nfs {{ $env }} {{ $deployment }}/.env'
    docker exec {{ $fpmContainerName }} bash -c 'cd {{ $deployment }} && ln -nfs {{ $storage }} {{ $deployment }}/storage'
    docker exec -u="www-data" {{ $fpmContainerName }} bash -c 'cd {{ $deployment }} && composer install --prefer-dist --no-dev'
    docker exec {{ $fpmContainerName }} bash -c 'cd {{ $deployment }} && php ./artisan migrate --force'
    docker exec {{ $fpmContainerName }} bash -c 'cd {{ $deployment }} && php ./artisan storage:link'
    docker exec {{ $fpmContainerName }} bash -c 'cd {{ $deployment }} && php ./artisan ca:cl'
    docker run --rm -u="node" -v="{{ $storage }}:{{ $storage }}" -v="{{ $deployment }}:{{ $deployment }}" -v="{{ $env }}:{{ $env }}" node:latest bash -c 'cd {{ $deployment }} && npm install && npm run prod'
@endtask

@task('live')
    cd {{ $deployment }}
    ln -nfs {{ $deployment }} {{ $serve }}
    cd {{ $builds }} && ls -1tr | head -n -3 | xargs -d '\n' rm -rf --
@endtask

