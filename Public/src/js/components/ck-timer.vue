<template>
    <div class="flex -mx-4 flex-wrap">
        <div class="px-4 w-full md:w-1/2 lg:w-1/3">
            <h1 class="text-2xl">Timer</h1>
            <div class="rounded shadow my-3 bg-white flex flex-col">
                <div v-if="cameraStarted">
                    <vue-web-cam 
                        @error="handleErrorOnCamera" 
                        @notsupported="handleErrorOnCamera"
                        @started="handleCameraStarted"
                        ref="webcam" 
                        @cameras="loadedCameras" :selectFirstDevice="true" :height="700"></vue-web-cam>
                </div>
                <div class="flex flex-col justify-center items-center py-8 bg-blue-800">
                    <div id="counter" :class=" started ? 'animate-pulse' : ''" class="flex text-white flex-col justify-center items-center text-6xl">
                        <h3 class="font-bold">{{ hours }}:{{ minutes }}:{{ seconds }}</h3>
                    </div>
                </div>
                <div class="flex" v-if="! cameraDisabled">
                    <template v-if="cameraStarted">
                        <div @click="start( 'working' )" v-if="! started" class="flex-auto w-full items-center flex justify-center bg-blue-900 text-white font-bold cursor-pointer py-4">Start Working</div>
                        <div @click="start( 'break' )" v-if="! started" class="flex-auto w-full items-center flex justify-center bg-teal-600 text-white font-bold cursor-pointer py-4">Start Break</div>
                        <div @click="stop( 'working' )" v-if="started && action === 'working'" class="flex-auto w-full items-center flex justify-center bg-red-600 text-white font-bold cursor-pointer py-4">Stop Working</div>
                        <div @click="stop( 'break' )" v-if="started && action === 'break'" class="flex-auto w-full items-center flex justify-center bg-red-600 text-white font-bold cursor-pointer py-4">Stop Break</div>
                    </template>
                    <div @click="cameraStarted = true" v-if="! cameraStarted" class="flex-auto w-full items-center flex justify-center bg-green-600 text-white font-bold cursor-pointer py-4">Start Camera</div>
                </div>
                <div class="flex" v-if="cameraDisabled">
                    <div class="flex-auto w-full justify-center items-center bg-gray-700 py-4 text-white font-bold flex">Camera Disabled / Missing</div>
                </div>
            </div>
        </div>
        <div class="px-4 w-full md:w-1/2 lg:w-2/3">
            <h1 class="text-2xl">History</h1>
            <div class="rounded shadow bg-white my-3">
                <div :key="history.id" v-for="history of histories.data" class="flex flex-col p-2 border-b border-gray-300 md:flex-row">
                    <div class="flex flex-col flex-auto">
                        <h3 class="text-gray-800 font-bold text-xl mb-2" v-if="history.action === 'working'">Working &mdash; <a :href="history.image_url_start" target="_blank" class="rounded-full px-2 py-1 rounded bg-white border text-sm font-normal">See Image</a></h3>
                        <h3 class="text-gray-800 font-bold text-xl mb-2" v-if="history.action === 'break'">Break &mdash; <a :href="history.image_url_start" target="_blank" class="rounded-full px-2 py-1 rounded bg-white border text-sm font-normal">See Image</a></h3>
                        <p class="text-gray-700 text-sm"><strong>From: </strong> {{ history.from_moment }} </p>
                        <p class="text-gray-700 text-sm"><strong>To: </strong> {{ history.to_moment }} </p>
                        <p class="text-gray-700 text-sm"><strong>Duration: </strong> {{ history.duration }} </p>
                    </div>
                    <div class="flex">
                        <img :src="history.image_url_start" class="w-32">
                    </div>
                </div>
                <div class="flex items-center justify-center py-4">
                    <h3 v-if="! histories.data || histories.data.length === 0" class="text-gray-800">No history registered</h3>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
import * as duration from 'moment-duration-format';
import { WebCam } from "vue-web-cam";
import { forkJoin } from 'rxjs';

export default {
    name: 'ck-timer',
    components: {
        WebCam
    },
    data: () => {
        return {
            cameraDisabled: false,
            cameraStarted: false,
            started: false,
            action: '',
            timer: null,
            histories: [],
            date: moment(),
            fullSeconds: 0,
            field: {
                label: 'Operation Type',
                name: 'operation_type',
                type: 'multiselect',
                options: [
                    {
                        label: 'Work',
                        value: 'bar'
                    }, {
                        label: 'Break',
                        value: 'foo'
                    }
                ]
            }
        }
    },
    computed: {
        fullDate() {
            return this.date.format();
        },
        hours() {
            return Math.floor(this.fullSeconds / 3600).toString().padStart(2,'0');
        },
        minutes() {
            return Math.floor(this.fullSeconds % 3600 / 60).toString().padStart(2,'0');
        },
        seconds() {
            return Math.floor(this.fullSeconds % 60).toString().padStart(2,'0');
        }
    },
    mounted() {
        duration( moment );
        this.loadExistingCounter();
    },
    methods: {
        handleCameraStarted() {
            this.cameraStarted      =   true;
            this.cameraDisabled     =   false;
        },
        handleErrorOnCamera( error ) {
            this.cameraDisabled     =   true;
        },
        dataURLtoFile(dataurl, filename) {
            const arr = dataurl.split(',')
            const mime = arr[0].match(/:(.*?);/)[1]
            const bstr = atob(arr[1])
            let n = bstr.length
            const u8arr = new Uint8Array(n)
            while (n) {
                u8arr[n - 1] = bstr.charCodeAt(n - 1)
                n -= 1 // to make eslint happy
            }
            return new File([u8arr], filename, { type: mime })
        },
        loadedCameras( cameras ) {
            console.log( cameras );
        },
        loadExistingCounter() {
            forkJoin(
                nsHttpClient.get( '/api/modules/clockin/get' ),
                nsHttpClient.get( '/api/modules/clockin/history' ),
            )
                .subscribe( result => {
                    /**
                     * if the timer has started
                     * this will automatically resume it
                     */
                    if ( result[0].status === 'failed' ) {
                        this.started    =   false;
                    } else {
                        this.fullSeconds    =   ns.date.moment.unix() - moment( result[0].from_moment ).unix();
                        this.started        =   true;
                        this.action         =   result[0].action;
                        this.continue();
                    }

                    this.buildHistories( result[1] );
                })
        },
        reloadHistory() {
            nsHttpClient.get( '/api/modules/clockin/history' )
                .subscribe( histories => {
                    this.buildHistories( histories );
                })
        },
        buildHistories( result ) {
            /**
             * this check wether there is 
             * an history for the provided time
             */
            this.histories      =   result;
            this.histories.data.forEach( time => {
                const seconds   =   moment( time.to_moment ).unix() - moment( time.from_moment ).unix();
                time.duration   =   moment.duration( seconds, 'seconds' ).format( 'h [hrs], m [min], s [sec]' );
            });
        },  
        removeOption( option, field ) {
            this.field.options.forEach( option => option.selected = false );
            field.showPanel     =   true;
        },
        selectOption( newOption ) {
            const index     =   this.field.options.indexOf( newOption );

            this.field.options.forEach( (option, _index ) => {
                if ( index !== _index ) {
                    option.selected     =   false;    
                } else {
                    option.selected     =   true;    
                }
            });
        },
        continue() {
            this.timer  =   setInterval( () => {
                this.fullSeconds++;
            }, 1000 );
        },
        start( action ) {
            const image     =   this.$refs.webcam.capture();
            const file      =   this.dataURLtoFile( image, 'capture' );
            const form      =   new FormData;
            
            form.append( 'file', file, file.name );
            form.append( 'action', action );

            nsHttpClient.post( '/api/modules/clockin/start', form, {
                'Content-Type' : 'multipart/form-data'
            })
                .subscribe( result => {
                    if ( result.status === 'success' ) {
                        this.action     =   action;
                        this.started    =   true;
                        this.timer  =   setInterval( () => {
                            this.fullSeconds++;
                        }, 1000 );
                    } else {
                        return nsSnackBar.error( result.message ).subscribe();
                    }
                });        
        },
        stop( action ) {
            const image     =   this.$refs.webcam.capture();
            const file      =   this.dataURLtoFile( image, 'capture' );
            const form      =   new FormData;
            
            form.append( 'file', file, file.name );
            form.append( 'action', action );

            nsHttpClient.post( '/api/modules/clockin/stop', form, {
                'Content-Type' : 'multipart/form-data'
            })
                .subscribe( result => { 
                    this.action         =   null;
                    this.fullSeconds    =   0;
                    this.started        =   false;
                    clearInterval( this.timer );
                    this.reloadHistory();
                }, error => {
                    nsSnackBar
                        .error( error.message )
                        .subscribe()
                }); 
        }
    }
}
</script>