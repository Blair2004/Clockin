<template>
    <div>
        <div id="worker-cards" class="-mx-4 flex flex-wrap">
            <div class="px-4 w-full md:w-1/3 mb-4">
                <div class="rounded-lg shadow-lg bg-transparent bg-gradient-to-tl from-blue-400 to-teal-400 p-4">
                    <h3 class="text-white font-bold text-xl">Working Time</h3>
                    <div class="flex -mx-1 items-end text-gray-100">
                        <div class="px-1">
                            <h2 class="font-bold text-6xl">{{ working_time.hours || 0 }}<span class="text-lg">h</span></h2>
                        </div>
                        <div class="px-1">
                            <h2 class="font-bold text-6xl">{{ working_time.minutes || 0 }}<span class="text-lg">min</span></h2>
                        </div>
                        <div class="px-1">
                            <h2 class="font-bold text-6xl">{{ working_time.seconds || 0 }}<span class="text-lg">sec</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 w-full md:w-1/3 mb-4">
                <div class="rounded-lg shadow-lg bg-transparent bg-gradient-to-br from-red-400 to-orange-400 p-4">
                    <h3 class="text-white font-bold text-xl">Rest Time</h3>
                    <div class="flex -mx-1 items-end text-gray-100">
                        <div class="px-1">
                            <h2 class="font-bold text-6xl">{{ break_time.hours || 0 }}<span class="text-lg">h</span></h2>
                        </div>
                        <div class="px-1">
                            <h2 class="font-bold text-6xl">{{ break_time.minutes || 0 }}<span class="text-lg">min</span></h2>
                        </div>
                        <div class="px-1">
                            <h2 class="font-bold text-6xl">{{ break_time.seconds || 0 }}<span class="text-lg">sec</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 w-full md:w-1/3 mb-4">
                <div class="rounded-lg shadow-lg bg-transparent bg-gradient-to-br from-indigo-400 to-green-400 p-4">
                    <h3 class="text-white font-bold text-xl">Best Working Session</h3>
                    <div class="flex -mx-1 items-end text-gray-100">
                        <div class="px-1">
                            <h2 class="font-bold text-6xl">{{ best_session_time.hours || 0 }}<span class="text-lg">h</span></h2>
                        </div>
                        <div class="px-1">
                            <h2 class="font-bold text-6xl">{{ best_session_time.minutes }}<span class="text-lg">min</span></h2>
                        </div>
                        <div class="px-1">
                            <h2 class="font-bold text-6xl">{{ best_session_time.seconds }}<span class="text-lg">sec</span></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="worker-selector" class="shadow-lg rounded-lg bg-white p-4 mb-4">
            <div class="-mx-4 flex flex-wrap rounded-lg">
                <div class="px-4 w-full md:w-1/3" v-for="(field, index) of fields" :key="index">
                    <ns-field :field="field"></ns-field>
                </div>
            </div>
            <div>
                <ns-button @click="loadReport()" type="info">Load</ns-button>
            </div>
        </div>
        <div class="rounded-lg shadow-lg mb-4 bg-white" v-if="result.history">
            <table class="w-full">
                <thead>
                    <tr class="text-gray-700">
                        <td class="p-2 border border-gray-400 bg-gray-100">Type</td>
                        <td class="p-2 border border-gray-400 bg-gray-100">Starting Time</td>
                        <td class="p-2 border border-gray-400 bg-gray-100">Ending Time</td>
                        <td class="p-2 border border-gray-400 bg-gray-100">Total Time</td>
                        <td class="p-2 border border-gray-400 bg-gray-100">Starting Image</td>
                        <td class="p-2 border border-gray-400 bg-gray-100">Ending Image</td>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    <tr v-for="history of result.history.data" :key="history.id">
                        <td class="p-2 bg-white border border-gray-400">{{ history.action }}</td>
                        <td class="p-2 bg-white border border-gray-400">{{ history.from_moment }}</td>
                        <td class="p-2 bg-white border border-gray-400">{{ history.to_moment }}</td>
                        <td class="p-2 bg-white border border-gray-400">{{ hoursPad( history.total_seconds ) + ':' + minutesPad( history.total_seconds ) + ':' + secondsPad( history.total_seconds ) }}</td>
                        <td class="p-2 bg-white border border-gray-400">
                            <a :href="history.image_url_start" target="_blank" class="rounded-lg border py-1 px-2 font-bold text-gray-700"><i class="las la-eye"></i> See Image</a>
                        </td>
                        <td class="p-2 bg-white border border-gray-400">
                            <a :href="history.image_url_end" target="_blank" class="rounded-lg border py-1 px-2 font-bold text-gray-700"><i class="las la-eye"></i> See Image</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div id="pagination" class="flex -mx-1">
                <template v-if="result.current_page">
                    <a href="javascript:void(0)" @click="page=result.first_page;loadReport()" class="mx-1 flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 text-gray-700 hover:bg-blue-400 hover:text-white shadow">
                        <i class="las la-angle-double-left"></i>
                    </a>
                    <template v-for="(_paginationPage, index) of pagination">
                        <a :key="index" v-if="page !== '...'" :class="page == _paginationPage ? 'bg-blue-400 text-white' : 'bg-gray-200 text-gray-700'" @click="page=_paginationPage;loadReport()" href="javascript:void(0)" class="mx-1 flex items-center justify-center h-8 w-8 rounded-full hover:bg-blue-400 hover:text-white">{{ _paginationPage }}</a>
                        <a :key="index" v-if="page === '...'" href="javascript:void(0)" class="mx-1 flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 text-gray-700">...</a>
                    </template>
                    <a href="javascript:void(0)" @click="page=result.last_page;loadReport()" class="mx-1 flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 text-gray-700 hover:bg-blue-400 hover:text-white shadow">
                        <i class="las la-angle-double-right"></i>
                    </a>
                </template>
            </div>
        </div>
    </div>
</template>
<script>
import FormValidation from '../../../../../../resources/js/libraries/form-validation'
export default {
    name: 'ck-report',
    data() {
        return {
            validation: new FormValidation,
            fields: [],
            result: new Object,
            page : 1,
            working_time: {},
            break_time: {},
            best_session_time: {}
        }
    },
    computed: {
        pagination() {
            if ( this.result.history ) {
                return this.pageNumbers( this.result.last_page, this.result.current_page );
            }
            return [];
        }
    },
    methods: {
        loadReport() {
            nsHttpClient.post( `/api/modules/clockin/report?page=${this.page}`, this.validation.extractFields( this.fields ) )
                .subscribe( result => {
                    this.result     =   result;
                    this.page       =   result.history.current_page;
                    this.working_time   =   {
                        hours       :   this.hoursPad( result.total_working_time ),
                        minutes     :   this.minutesPad( result.total_working_time ),
                        seconds     :   this.secondsPad( result.total_working_time ),
                    };
                    this.break_time   =   {
                        hours       :   this.hoursPad( result.total_break_time ),
                        minutes     :   this.minutesPad( result.total_break_time ),
                        seconds     :   this.secondsPad( result.total_break_time ),
                    }
                    this.best_session_time  =   {
                        hours: this.hoursPad( result.best_session.total_seconds ),
                        minutes: this.minutesPad( result.best_session.total_seconds ),
                        seconds: this.secondsPad( result.best_session.total_seconds ),
                    }
                })
        },
        hoursPad( seconds ) {
            return Math.floor( seconds / 3600).toString().padStart(2,'0');
        },
        minutesPad( seconds ) {
            return Math.floor( seconds % 3600 / 60).toString().padStart(2,'0');
        },
        secondsPad( seconds ) {
            return Math.floor( seconds % 60).toString().padStart(2,'0');
        },
        pageNumbers(count, current) {
            var shownPages = 3;
            var result = [];
            if (current > count - shownPages) {
                result.push(count - 2, count - 1, count);
            } else {
                result.push(current, current + 1, current + 2, '...', count);
            }
            return result.filter( f => f > 0 || typeof f === 'string' );
        },
    },
    mounted() {
        nsHttpClient.get( '/api/nexopos/v4/forms/ck.report' )
            .subscribe( result => {
                this.fields     =   this.validation.createFields( result.tabs.general.fields );
            })
    }
}
</script>