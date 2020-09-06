<template>
    <div class="flex -mx-4 flex-wrap">
        <div class="px-4 w-full md:w-1/2 lg:w-1/3">
            <h1 class="text-2xl">Timer</h1>
            <div class="rounded shadow my-3 bg-white flex flex-col">
                <div>
                    <web-cam :selectFirstDevice="true" :height="700"></web-cam>
                </div>
                <div class="flex flex-col justify-center items-center py-8 bg-blue-800">
                    <div id="counter" :class=" started ? 'animate-pulse' : ''" class="flex text-white flex-col justify-center items-center text-6xl">
                        <h3 class="font-bold">{{ hours }}:{{ minutes }}:{{ seconds }}</h3>
                    </div>
                </div>
                <div class="flex">
                    <div @click="start( 'working' )" v-if="! started" class="flex-auto w-full items-center flex justify-center bg-blue-900 text-white font-bold cursor-pointer py-4">Start Working</div>
                    <div @click="start( 'break' )" v-if="! started" class="flex-auto w-full items-center flex justify-center bg-teal-600 text-white font-bold cursor-pointer py-4">Start Break</div>
                    <div @click="stop( 'working' )" v-if="started && action === 'working'" class="flex-auto w-full items-center flex justify-center bg-red-600 text-white font-bold cursor-pointer py-4">Stop Working</div>
                    <div @click="stop( 'break' )" v-if="started && action === 'break'" class="flex-auto w-full items-center flex justify-center bg-red-600 text-white font-bold cursor-pointer py-4">Stop Break</div>
                </div>
            </div>
        </div>
        <div class="px-4 w-full md:w-1/2 lg:w-1/3">
            <h1 class="text-2xl">History</h1>
            <div class="rounded shadow bg-white my-3">
                <div :key="history.id" v-for="history of histories.data" class="flex flex-col p-2 border-b border-gray-300">
                    <h3 class="text-gray-800 font-bold text-xl" v-if="history.action === 'working'">Working</h3>
                    <h3 class="text-gray-800 font-bold text-xl" v-if="history.action === 'break'">Break</h3>
                    <p class="text-gray-700 text-sm"><strong>From: </strong> {{ history.from_moment }} </p>
                    <p class="text-gray-700 text-sm"><strong>To: </strong> {{ history.to_moment }} </p>
                    <p class="text-gray-700 text-sm"><strong>Duration: </strong> {{ history.duration }} </p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
import { WebCam } from "vue-web-cam";

export default {
    name: 'ck-timer',
    components: {
        WebCam
    },
    data: () => {
        return {
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
            nsHttpClient.post( '/api/modules/clockin/start', { action })
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
            nsHttpClient.post( '/api/modules/clockin/stop', { action })
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