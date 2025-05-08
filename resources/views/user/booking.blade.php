<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Car Booking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Available Cars</h3>

                    <!-- Car List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Sample Car Card -->
                        <div class="border rounded-lg overflow-hidden">
                            <div class="p-4">
                                <h4 class="font-semibold text-lg mb-2">Toyota Avanza</h4>
                                <p class="text-gray-600 mb-2">Capacity: 7 Persons</p>
                                <p class="text-gray-600 mb-4">Rate: Rp 500.000/day</p>

                                <!-- Booking Form -->
                                <form class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Pick-up Date</label>
                                        <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Return Date</label>
                                        <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>

                                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Book Now
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Add more car cards here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
