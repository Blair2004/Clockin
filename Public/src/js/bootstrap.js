const WebCam    =   require( 'vue-web-cam' );

nsExtraComponents.CkTimer           =   require( './components/ck-timer.vue' ).default;
nsExtraComponents.CkReport          =   require( './components/ck-report.vue' ).default;
nsExtraComponents[ 'vue-web-cam' ]  =   WebCam;

console.log( nsExtraComponents );