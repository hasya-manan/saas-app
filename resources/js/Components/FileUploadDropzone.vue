<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: File,
    accept: {
        type: String,
        default: '.pdf,.jpg,.jpeg,.png'
    }
});

const emit = defineEmits(['update:modelValue']);

const fileInput = ref(null);
const isDragging = ref(false);

// Trigger file browser when clicking the box or browse button
const openFileBrowser = () => {
    fileInput.value.click();
};

// Handle file selection from browse dialog
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        emit('update:modelValue', file);
    }
};

// Handle drag and drop events
const handleDrop = (event) => {
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    if (file) {
        emit('update:modelValue', file);
    }
};

const removeFile = () => {
    emit('update:modelValue', null);
    if (fileInput.value) fileInput.value.value = '';
};
</script>

<template>
    <div 
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
        @click="openFileBrowser"
        :class="[
            'relative border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer transition-all flex flex-col items-center justify-center',
            isDragging ? 'border-primary bg-primary/5 scale-[1.01]' : 'border-indigo-200/80 bg-indigo-50/20 hover:bg-indigo-50/40 hover:border-primary/50'
        ]"
    >
        <!-- Hidden Native File Input -->
        <input 
            ref="fileInput"
            type="file"
            class="hidden"
            :accept="accept"
            @change="handleFileChange"
        />

        <!-- Default State: Dropzone prompt -->
        <div v-if="!modelValue" class="space-y-3 pointer-events-none">
            <div class="text-primary font-bold text-base tracking-wide">
                Drop anything here or browse
            </div>
            <p class="text-xs text-gray-400">
                Docs, images, PDFs & more
            </p>

            <!-- Action Pills / Icons (Visual Only, clicking box handles upload) -->
            <div class="flex items-center justify-center gap-3 pt-2">
                <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center shadow-md shadow-primary/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center shadow-md shadow-primary/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
                <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center shadow-md shadow-primary/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>
        </div>

        <!-- File Selected State (Shows Preview Card like your image) -->
        <div v-else class="flex items-center gap-3 bg-white px-4 py-3 rounded-xl shadow-sm border border-indigo-100 w-full max-w-xs relative z-10" @click.stop>
            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold overflow-hidden shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="overflow-hidden text-left flex-1">
                <p class="text-xs font-bold text-gray-800 truncate">{{ modelValue.name }}</p>
                <p class="text-[10px] text-gray-400">Ready to upload</p>
            </div>
            <!-- Remove Button -->
            <button @click.stop="removeFile" class="text-gray-400 hover:text-red-500 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
</template>