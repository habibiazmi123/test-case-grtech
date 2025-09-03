<script setup>
import { Modal } from 'ant-design-vue';
import { ref, watch } from 'vue';
import { LinkOutlined } from '@ant-design/icons-vue';

const props = defineProps({
    open: Boolean,
    company: Object,
});

const emit = defineEmits(['update:open']);

const visible = ref(props.open);

watch(
    () => props.open,
    (val) => (visible.value = val)
);

const close = () => emit('update:open', false);
</script>

<template>
    <Modal v-model:open="visible" title="Company Info" :footer="null" @cancel="close">
        <div v-if="company" class="pt-4 flex items-center gap-2">
            <img
                :src="company.logo"
                :alt="company.name"
                class="w-16 h-16 rounded-lg object-cover shadow-md border"
            />

            <div class="flex justify-between w-full">
                <div>
                    <div class="font-bold text-gray-900">{{ company.name }}</div>
                    <a :href="`mailto:${company.email}`" class="text-sm text-gray-500">{{
                        company.email
                    }}</a>
                </div>

                <a
                    :href="company.website"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-1 text-indigo-600 hover:underline flex items-center self-start"
                >
                    <LinkOutlined />
                    <span class="ml-1 text-sm">Visit</span>
                </a>
            </div>
        </div>
    </Modal>
</template>
