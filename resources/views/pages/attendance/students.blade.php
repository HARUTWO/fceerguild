<x-layouts.app :title="__('Student Attendance')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        @livewire('attendance.students')
    </div>

    {{-- Modals --}}
    @livewire('attendance.export-modal')
    @livewire('attendance.review-season-modal')
</x-layouts.app>
