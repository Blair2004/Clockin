@extends( 'layout.dashboard' )

@section( 'layout.dashboard.body' )
    <div class="h-full flex flex-col flex-auto" id="dashboard-content">
        @include( '../common/dashboard-header' )
        <div class="px-4 flex-auto flex flex-col">
            <ck-timer></ck-timer>
        </div>
    </div>
@endsection

@section( 'layout.dashboard.footer.inject' )
    <script src="{{ asset( 'modules/clockin/dist/bootstrap.js' ) }}"></script>
@endsection