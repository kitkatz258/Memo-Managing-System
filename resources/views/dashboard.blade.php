@extends('layouts.admin')

@section('content')
    
    <livewire:dashboard-stats />

    <livewire:modals.memo-form-modal />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('uploadsChart');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Uploads',
                    data: @json($data),
                    borderWidth: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        </script>
@endsection