@extends('layouts.dashboard')

@section('breadcrum')
Ticket Management
@endsection

@section('content')

@include('topmessages')
<div class="row justify-content-center g-3">
    <form method="get" action="">
        <div style="display:inline-flex;margin-right:10px;">
            <select class="form-select mx-2" name="sel_role_id" style="display:inline;width:auto !important;">
                <option value="1" selected>BUY </option>
                <option value="2">SELL </option>
            </select>
        </div>
    </form>

    <div class="col-xl-12">
        <div class="row g-3">
            <div class="col-12 col-md-12 col-xl-12 pt-3">
                <div class="card card-one card-product text-center">
                    <div class="card-body p-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="bg-success text-white">AMC Name</th>
                                    <th class="bg-success text-white">Symbol</th>
                                    <th class="bg-success text-white">Quick Ticket </th>
                                    <th class="bg-success text-white">NAV</th>
                                    <th class="bg-success text-white">Quick Ticket Value</th>
                                    <th class="bg-success text-white">Ticket Raised</th>
                                    <th class="bg-success text-white">Amount Sent</th>
                                    <th class="bg-success text-white">Total Units</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- row -->
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        function formatDate(dateString) {
            const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
            const date = new Date(dateString);
            return date.toLocaleDateString('en-CA', options); // 'en-CA' gives YYYY-MM-DD format
        }

        function loadData(selectedValue) {
            $.ajax({
                url: '{{ route("trader.mis.ajax") }}',
                type: 'GET',
                data: { sel_role_id: selectedValue },
                success: function(data) {
                    var table = $('table.table');
                    var thead = table.find('thead');
                    var tbody = table.find('tbody');
                    tbody.empty();

                    // Initialize the total amount
                    var totalTicket = 0;
                    var totalQuickTicketVal = 0;
                    var totalQuickTicketUnits = 0;
                    var amountReceived = 0;

                    // Change table headers based on the selected value
                    if (selectedValue == 1) { // BUY case
                        thead.html(
                            '<tr>' +
                                '<th class="bg-success text-white">AMC Name</th>' +
                                '<th class="bg-success text-white">Symbol</th>' +
                                '<th class="bg-success text-white">Quick Ticket </th>' +
                                '<th class="bg-success text-white">NAV</th>' +
                                '<th class="bg-success text-white">Quick Ticket Value</th>' +
                                '<th class="bg-success text-white">Ticket Raised</th>' +
                                '<th class="bg-success text-white">Amount Sent</th>' +
                                '<th class="bg-success text-white">Total Units</th>' +
                            '</tr>'
                        );

                        data.forEach(function(row) {
                            totalTicket += parseFloat(row.total_basket_no);
                            totalQuickTicketVal += parseFloat(row.total_amt);
                            totalQuickTicketUnits += parseFloat(row.total_units)

                            var totalNavPerClubbed = (row.total_nav / row.total_clubbed) === 0 ? '-' : (row.total_nav / row.total_clubbed);
                            var totalBasketNo = row.total_basket_no;

                            var navPerClubbedCell = row.source === 'quick_ticket' ? totalNavPerClubbed : '-';
                            var basketNoCell = row.source === 'quick_ticket' ? totalBasketNo : '-';
                            var ticketBasketNoCell = row.source === 'ticket' ? totalBasketNo : '-';
                            var ticketActualAmt = row.source === 'ticket' ? row.total_amt : '-';

                            var tr = '<tr>' +
                                '<td>' + row.security.amc.name + '</td>' +  // AMC Name
                                '<td>' + row.security.symbol + '</td>' +  // Symbol
                                '<td>' + basketNoCell + '</td>' +  // Total Basket No
                                '<td>' + navPerClubbedCell + '</td>' +  // NAV / Clubbed
                                '<td>' + (row.total_amt == 0 ? '-' : row.total_amt) + '</td>' +
                                '<td>' + ticketBasketNoCell + '</td>' +  // Placeholder for additional fields
                                '<td>' + ticketActualAmt + '</td>' +  // Placeholder for additional fields
                                '<td>' + row.total_units + '</td>' +  // Total Units
                                '</tr>';
                            tbody.append(tr);
                        });

                        // Append the total row
                        var totalRow = '<tr style="background: grey; color: white;">' +
                            '<td colspan="2">Total</td>' +
                            '<td>' + totalTicket.toFixed(2) + '</td>' +
                            '<td></td>' +
                            '<td>' + totalQuickTicketVal.toFixed(2) + '</td>' +
                            '<td>' + '' + '</td>' +
                            '<td>' + '' + '</td>' +
                            '<td>' + totalQuickTicketUnits.toFixed(2) + '</td>' +
                            '</tr>';
                        tbody.append(totalRow);

                    } else if (selectedValue == 2) { // SELL case
                        thead.html(
                            '<tr>' +
                                '<th class="bg-danger text-white">AMC Name</th>' +
                                '<th class="bg-danger text-white">Symbol</th>' +
                                '<th class="bg-danger text-white">Quick Ticket </th>' +
                                '<th class="bg-danger text-white">NAV</th>' +
                                '<th class="bg-danger text-white">Quick Ticket Value</th>' +
                                '<th class="bg-danger text-white">Ticket Raised</th>' +
                                '<th class="bg-danger text-white">Amount Sent</th>' +
                                '<th class="bg-danger text-white">Total Units</th>' +
                            '</tr>'
                        );

                        data.forEach(function(row) {
                            totalTicket += parseFloat(row.total_basket_no);
                            totalQuickTicketVal += parseFloat(row.total_amt);
                            totalQuickTicketUnits += parseFloat(row.total_units)

                            var totalNavPerClubbed = (row.total_nav / row.total_clubbed) === 0 ? '-' : (row.total_nav / row.total_clubbed);
                            var totalBasketNo = row.total_basket_no;

                            var navPerClubbedCell = row.source === 'quick_ticket' ? totalNavPerClubbed : '-';
                            var basketNoCell = row.source === 'quick_ticket' ? totalBasketNo : '-';
                            var ticketBasketNoCell = row.source === 'ticket' ? totalBasketNo : '-';
                            var ticketActualAmt = row.source === 'ticket' ? row.total_amt : '-';

                            var tr = '<tr>' +
                                '<td>' + row.security.amc.name + '</td>' +  // AMC Name
                                '<td>' + row.security.symbol + '</td>' +  // Symbol
                                '<td>' + basketNoCell + '</td>' +  // Total Basket No
                                '<td>' + navPerClubbedCell + '</td>' +  // NAV / Clubbed
                                '<td>' + (row.total_amt == 0 ? '-' : row.total_amt) + '</td>' +
                                '<td>' + ticketBasketNoCell + '</td>' +  // Placeholder for additional fields
                                '<td>' + ticketActualAmt + '</td>' +  // Placeholder for additional fields
                                '<td>' + row.total_units + '</td>' +  // Total Units
                                '</tr>';
                            tbody.append(tr);
                        });

                        // Append the total row
                        var totalRow = '<tr style="background: grey; color: white;">' +
                            '<td colspan="2">Total</td>' +
                            '<td>' + '-' + '</td>' +
                            '<td></td>' +
                            '<td>' + '-' + '</td>' +
                            '<td>' + '-' + '</td>' +
                            '<td>' + '-' + '</td>' +
                            '<td>' + '-' + '</td>' +
                            '</tr>';
                        tbody.append(totalRow);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        // Trigger change event on page load
        var defaultValue = $('select[name="sel_role_id"]').val();
        loadData(defaultValue);

        $('select[name="sel_role_id"]').change(function() {
            var selectedValue = $(this).val();
            loadData(selectedValue);
        });
    });
</script>
@endsection
