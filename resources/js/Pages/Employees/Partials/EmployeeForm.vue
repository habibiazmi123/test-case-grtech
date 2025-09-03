<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Modal } from 'ant-design-vue';
import { onMounted, ref } from 'vue';

const page = usePage();
const props = defineProps({
    open: Boolean,
    employee: {
        type: Object,
        default: null,
    },
});
const emit = defineEmits(['update:open']);

const { companies } = page.props.filters;

const confirmLoading = ref(false);

const form = useForm({
    company_id: props.employee?.company?.id || '',
    first_name: props.employee?.first_name || '',
    last_name: props.employee?.last_name || '',
    email: props.employee?.email || '',
    phone: props.employee?.phone || '',
});

const submit = () => {
    confirmLoading.value = true;

    let url = props.employee
        ? route('employees.update', props.employee.id)
        : route('employees.store');

    form.post(url, {
        forceFormData: true,
        onSuccess: () => {
            closeModal();
            emit('refresh');
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
        :title="`${employee ? 'Edit' : 'Add'} Employee`"
        :ok-text="`${employee ? 'Update' : 'Save'}`"
        cancel-text="Cancel"
        :confirm-loading="confirmLoading"
        :mask-closable="!confirmLoading"
        width="600px"
        @ok="submit"
        @cancel="closeModal"
    >
        <form @submit.prevent="submit" class="py-2">
            <div class="mb-2">
                <InputLabel for="company" value="Company" />

                <select
                    id="company"
                    v-model="form.company_id"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full mt-1"
                >
                    <option value="">-- Select Company --</option>
                    <option :value="company.id" :key="company.id" v-for="company in companies">
                        {{ company.name }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.company_id" />
            </div>

            <div class="flex gap-4 mb-2">
                <div class="w-full">
                    <InputLabel for="first_name" value="First name" />

                    <TextInput
                        id="first_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.first_name"
                        required
                        autofocus
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.first_name" />
                </div>

                <div class="w-full">
                    <InputLabel for="last_name" value="Last name" />

                    <TextInput
                        id="last_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.last_name"
                        required
                        autofocus
                        autocomplete="last_name"
                    />

                    <InputError class="mt-2" :message="form.errors.last_name" />
                </div>
            </div>

            <div class="flex gap-4 mb-2">
                <div class="w-full">
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="w-full">
                    <InputLabel for="phone" value="Phone" />

                    <TextInput
                        id="phone"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.phone"
                    />

                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
            </div>

            <button type="submit" class="hidden"></button>
        </form>
    </Modal>
</template>
