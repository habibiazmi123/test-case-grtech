<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button, Modal, Space, Table } from 'ant-design-vue';
import {
    DeleteOutlined,
    EditOutlined,
    ExclamationCircleOutlined,
    SearchOutlined,
} from '@ant-design/icons-vue';
import { h, onMounted, ref, watch } from 'vue';
import { debounce } from 'lodash';
import CompanyInfoModal from './Partials/CompanyInfoModal.vue';
import EmployeeForm from './Partials/EmployeeForm.vue';

const props = defineProps({
    employees: Object,
    filters: Object,
});

const [modal, contextHolder] = Modal.useModal();

const employeeModalOpen = ref(false);
const companyInfoModalOpen = ref(false);
const selectedEmployee = ref(null);

const loading = ref(false);
const search = ref('');
const companyId = ref('');

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    search.value = params.get('search') || '';
    companyId.value = params.get('company_id') || '';
});

watch(
    () => employeeModalOpen.value,
    (visible) => {
        if (!visible && selectedEmployee.value) {
            selectedEmployee.value = null;
        }
    }
);

const fetchData = (extra = {}) => {
    loading.value = true;
    router.get(
        route('employees.index'),
        {
            search: search.value,
            company_id: companyId.value,
            ...extra,
        },
        {
            preserveState: true,
            replace: true,
            onFinish: () => (loading.value = false),
        }
    );
};

const onShowCompanyInfo = (employee) => {
    selectedEmployee.value = employee;
    companyInfoModalOpen.value = true;
};

const onSearch = debounce(() => fetchData(), 400);

const onFilterCompany = () => fetchData();

const onTableChange = (pagination) =>
    fetchData({ page: pagination.current, per_page: pagination.pageSize });

const showConfirmDeletion = (employee) => {
    modal.confirm({
        title: 'Are you sure delete this employee?',
        icon: h(ExclamationCircleOutlined),
        content: h('div', { class: 'font-bold text-red-500' }, employee.full_name),
        okText: 'Yes',
        okType: 'danger',
        cancelText: 'No',
        onOk() {
            router.delete(route('employees.destroy', employee.id), {
                preserveScroll: true,
                onFinish: () => {
                    const { current_page, per_page } = props.employees.meta;
                    fetchData({ page: current_page, per_page });
                },
            });
        },
    });
};

const onEdit = (employee) => {
    selectedEmployee.value = employee;
    employeeModalOpen.value = true;
};
</script>

<template>
    <Head title="Employees" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Employees</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-xl font-semibold text-gray-900">Employee</h1>
                        <p class="mt-2 text-sm text-gray-700">A list of all the Employee.</p>
                    </div>

                    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                        <PrimaryButton @click="employeeModalOpen = true">
                            Add Employee
                        </PrimaryButton>
                    </div>
                </div>

                <div class="flex flex-col gap-4 justify-start sm:flex-row mt-6">
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

                    <div>
                        <select
                            v-model="companyId"
                            @change="onFilterCompany"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">-- Filter By Company --</option>
                            <option
                                :value="company.id"
                                :key="company.id"
                                v-for="company in filters.companies"
                            >
                                {{ company.name }}
                            </option>
                        </select>
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
                                title: 'Full name',
                                width: 200,
                                dataIndex: 'name',
                                customRender: ({ record }) =>
                                    h('div', { class: 'flex flex-col' }, [
                                        h(
                                            'span',
                                            { class: 'font-medium text-gray-900' },
                                            record.full_name
                                        ),
                                        h(
                                            'span',
                                            { class: 'text-sm text-gray-500' },
                                            record.email ?? '-'
                                        ),
                                    ]),
                            },
                            {
                                title: 'Company name',
                                dataIndex: 'company',
                                customRender: ({ record }) =>
                                    record.company
                                        ? h(
                                              'a',
                                              {
                                                  class: 'text-indigo-600 hover:underline',
                                                  onClick: () => onShowCompanyInfo(record),
                                              },
                                              record.company.name
                                          )
                                        : '',
                            },
                            {
                                title: 'Phone',
                                dataIndex: 'phone',
                                customRender: ({ record }) => record.phone ?? '-',
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
                        :data-source="employees.data"
                        :pagination="{
                            current: employees.meta.current_page,
                            pageSize: employees.meta.per_page,
                            total: employees.meta.total,
                        }"
                        row-key="id"
                        :loading="loading"
                        @change="onTableChange"
                    />
                </div>
            </div>
        </div>

        <EmployeeForm
            v-if="employeeModalOpen"
            v-model:open="employeeModalOpen"
            :employee="selectedEmployee"
            @refresh="fetchData()"
        />

        <CompanyInfoModal
            v-model:open="companyInfoModalOpen"
            :company="selectedEmployee?.company"
        />

        <contextHolder />
    </AuthenticatedLayout>
</template>
