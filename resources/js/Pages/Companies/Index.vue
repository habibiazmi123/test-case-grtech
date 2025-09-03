<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button, Table, Space, Modal } from 'ant-design-vue';
import {
    DeleteOutlined,
    EditOutlined,
    ExclamationCircleOutlined,
    SearchOutlined,
} from '@ant-design/icons-vue';
import { h, onMounted, ref, watch } from 'vue';
import { debounce } from 'lodash';
import CompanyForm from './Partials/CompanyForm.vue';

const props = defineProps({
    companies: Object,
});

const [modal, contextHolder] = Modal.useModal();

const companyModalOpen = ref(false);
const selectedCompany = ref(null);
const loading = ref(false);
const search = ref('');

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    search.value = params.get('search') || '';
});

watch(
    () => companyModalOpen.value,
    (visible) => {
        if (!visible && selectedCompany.value) {
            selectedCompany.value = null;
        }
    }
);

const fetchData = (extra = {}) => {
    loading.value = true;
    router.get(
        route('companies.index'),
        {
            search: search.value,
            ...extra,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            onFinish: () => (loading.value = false),
        }
    );
};

const onSearch = debounce(() => fetchData(), 400);
const onTableChange = (pagination) =>
    fetchData({ page: pagination.current, per_page: pagination.pageSize });
const showConfirmDeletion = (company) => {
    modal.confirm({
        title: 'Are you sure delete this company?',
        icon: h(ExclamationCircleOutlined),
        content: h('div', { class: 'font-bold text-red-500' }, company.name),
        okText: 'Yes',
        okType: 'danger',
        cancelText: 'No',
        onOk() {
            router.delete(route('companies.destroy', company.id), {
                preserveScroll: true,
                onFinish: () => {
                    const { current_page, per_page } = props.companies.meta;
                    fetchData({ page: current_page, per_page });
                },
            });
        },
    });
};
const onEdit = (company) => {
    selectedCompany.value = company;
    companyModalOpen.value = true;
};
</script>

<template>
    <Head title="Companies" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Companies</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-xl font-semibold text-gray-900">Company</h1>
                        <p class="mt-2 text-sm text-gray-700">A list of all the Company.</p>
                    </div>

                    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                        <PrimaryButton @click="companyModalOpen = true">
                            Add Company
                        </PrimaryButton>
                    </div>
                </div>

                <div class="flex flex-col justify-start sm:flex-row mt-6">
                    <div class="relative text-sm text-gray-800 col-span-3">
                        <div
                            class="absolute pl-2 left-0 top-0 bottom-0 flex items-center pointer-events-none text-gray-500"
                        >
                            <SearchOutlined />
                        </div>

                        <TextInput
                            type="search"
                            v-model="search"
                            @input="onSearch"
                            class="pl-8 w-full md:w-[300px]"
                        />
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg mt-6">
                    <Table
                        :columns="[
                            {
                                title: '#',
                                key: 'index',
                                customRender: ({ index }) => `${index + 1}.`,
                                width: 10,
                                align: 'center',
                            },
                            {
                                title: 'LOGO',
                                dataIndex: 'logo',
                                width: 100,
                                customRender: ({ record }) =>
                                    h('img', {
                                        src: record.logo,
                                        alt: record.name,
                                        class: 'w-20 h-20 object-contain rounded-xl',
                                    }),
                            },
                            {
                                title: 'NAME',
                                width: 200,
                                dataIndex: 'name',
                                customRender: ({ record }) =>
                                    h('div', { class: 'flex flex-col' }, [
                                        h(
                                            'span',
                                            { class: 'font-medium text-gray-900' },
                                            record.name
                                        ),
                                        h(
                                            'span',
                                            { class: 'text-sm text-gray-500' },
                                            record.email || '-'
                                        ),
                                    ]),
                            },
                            {
                                title: 'WEBSITE',
                                dataIndex: 'website',
                                customRender: ({ record }) =>
                                    h(
                                        'a',
                                        {
                                            href: record.website,
                                            target: '_blank',
                                            class: 'text-indigo-600 hover:underline',
                                        },
                                        record.website || '-'
                                    ),
                            },
                            {
                                title: 'Actions',
                                key: 'actions',
                                width: 160,
                                align: 'center',
                                customRender: ({ record }) =>
                                    h(Space, {}, () => [
                                        h(Button, {
                                            type: 'primary',
                                            size: 'medium',
                                            icon: h(EditOutlined),
                                            onClick: () => onEdit(record),
                                            class: 'flex justify-center items-center',
                                        }),
                                        h(Button, {
                                            type: 'primary',
                                            danger: true,
                                            size: 'medium',
                                            icon: h(DeleteOutlined),
                                            onClick: () => showConfirmDeletion(record),
                                            class: 'flex justify-center items-center',
                                        }),
                                    ]),
                            },
                        ]"
                        :data-source="companies.data"
                        :pagination="{
                            current: companies.meta.current_page,
                            pageSize: companies.meta.per_page,
                            total: companies.meta.total,
                        }"
                        row-key="id"
                        :loading="loading"
                        @change="onTableChange"
                    />
                </div>
            </div>
        </div>

        <CompanyForm
            v-if="companyModalOpen"
            v-model:open="companyModalOpen"
            :company="selectedCompany"
            @refresh="fetchData()"
        />

        <contextHolder />
    </AuthenticatedLayout>
</template>
