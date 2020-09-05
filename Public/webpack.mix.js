const mix   =   require( 'laravel-mix' );

mix.disableNotifications();
mix.js( 'src/js/bootstrap.js', 'dist' )
    .extract([ 
        'vue', 
        'lodash', 
        'chart.js', 
        'axios', 
        'moment', 
        'rxjs', 
        'rx', 
        'vue-router', 
        'dayjs' 
    ]);