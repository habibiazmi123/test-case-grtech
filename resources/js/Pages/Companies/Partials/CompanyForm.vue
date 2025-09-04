<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { Modal } from 'ant-design-vue';
import { computed, ref } from 'vue';
import { FileImageOutlined } from '@ant-design/icons-vue';

const props = defineProps({
    open: Boolean,
    company: {
        type: Object,
        default: null,
    },
});
const emit = defineEmits(['update:open']);

const confirmLoading = ref(false);
const previewLogoUrl = ref(props.company?.logo || null);

const hasLogo = computed(() => {
    return previewLogoUrl.value && !previewLogoUrl.value.toLowerCase().includes('no-image');
});

const form = useForm({
    name: props.company?.name || '',
    email: props.company?.email || '',
    logo: null,
    website: props.company?.website || '',
});

const handleLogoChange = (event) => {
    const file = event.target.files[0];

    if (file) {
        form.logo = file;
        previewLogoUrl.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    confirmLoading.value = true;

    let url = props.company
        ? route('companies.update', props.company.id)
        : route('companies.store');

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeModal();
        },
        onFinish: () => {
            confirmLoading.value = false;
        },
    });
};

const closeModal = () => {
    emit('update:open', false);
    form.reset();
};
</script>

<template>
    <Modal
        :open="open"
        :title="`${company ? 'Edit' : 'Add'} Company`"
        :ok-text="`${company ? 'Update' : 'Save'}`"
        cancel-text="Cancel"
        :confirm-loading="confirmLoading"
        :mask-closable="!confirmLoading"
        @ok="submit"
        @cancel="closeModal"
    >
        <form @submit.prevent="submit" class="py-2">
            <div class="mb-2">
                <InputLabel for="logo" value="Logo" />
                <input
                    type="file"
                    id="logo"
                    @change="handleLogoChange"
                    accept="image/*"
                    class="hidden"
                />

                <label
                    for="logo"
                    :class="[
                        'group mt-2 cursor-pointer relative flex items-center justify-center rounded-md border-dashed text-center w-32 h-32 overflow-hidden',
                        previewLogoUrl && hasLogo ? 'border-0' : 'border',
                    ]"
                >
                    <div v-if="previewLogoUrl && hasLogo" class="relative h-full w-full">
                        <img :src="previewLogoUrl" class="h-32 w-32 object-contain rounded-lg" />
                        <span
                            class="absolute bottom-0 left-0 right-0 text-white bg-black bg-opacity-60 text-sm text-center opacity-0 transition-opacity duration-300 ease-in-out group-hover:opacity-100"
                        >
                            Choose file
                        </span>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center">
                        <FileImageOutlined class="text-xl" />
                        <span class="block text-sm font-semibold">Upload file</span>
                    </div>
                </label>

                <InputError class="mt-2" :message="form.errors.logo" />
            </div>

            <div class="mb-2">
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mb-2">
                <InputLabel for="email" value="Email" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mb-2">
                <InputLabel for="website" value="Website" />

                <TextInput
                    id="website"
                    type="url"
                    class="mt-1 block w-full"
                    v-model="form.website"
                />

                <InputError class="mt-2" :message="form.errors.website" />
            </div>

            <button type="submit" class="hidden"></button>
        </form>
    </Modal>
</template>
