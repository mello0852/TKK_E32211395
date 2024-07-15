    {{-- Panggil file jquery untuk proses reatime --}}
    <script type="text/javascript" src="{{ asset('jquery/jquery.min.js') }}"></script>
    
    {{-- ajax untuk realtime --}}
    <script type="text/javascript">
        $(document).ready(function(){
            setInterval(function(){
                $("#voltage").load("{{ url('bacavoltage') }}");
                $("#power").load("{{ url('bacapower') }}");
                $("#power_factor").load("{{ url('bacapowerfactor') }}");
                $("#energy").load("{{ url('bacaenergy') }}");
                $("#current").load("{{ url('bacacurrent') }}");
                $("#biaya").load("{{ url('bacabiaya') }}");
            }, 1000);
        });
    </script>