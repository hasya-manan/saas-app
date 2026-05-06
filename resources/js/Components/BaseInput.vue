<script setup>
defineProps({
  modelValue: [String, Number],
  type: { type: String, default: 'text' },
  placeholder: String,
  error: String
});
defineEmits(['update:modelValue']);
</script>

<template>
  <div>
    <input
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :type="type"
      :placeholder="placeholder"
      class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
      :class="{ 'border-red-500 focus:ring-red-500': error }"
    />
    <p v-if="error" class="text-red-500 text-xs mt-1">{{ error }}</p>
  </div>
</template>

