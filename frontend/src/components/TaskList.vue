<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const tasks = ref([]);
const title = ref('');
const titleError = ref('');
const statusFilter = ref('');
const viewFilter = ref('active');
const currentPage = ref(1);
const totalPages = ref(1);
const totalTasks = ref(0);

const loadTasks = async (page = 1) => {
  const params = { page };
  // viewFilter: 'active' (default), 'trashed', 'all'
  if (viewFilter.value === 'trashed') {
    params.trashed = 'only';
  } else if (viewFilter.value === 'all') {
    params.trashed = 'with';
  }
  if (statusFilter.value) {
    params.status = statusFilter.value;
  }
  const response = await api.get('/tasks', { params });
  tasks.value = response.data.data;
  currentPage.value = response.data.meta.current_page;
  totalPages.value = response.data.meta.last_page;
  totalTasks.value = response.data.meta.total;
};

const createTask = async () => {
  titleError.value = '';

  const val = title.value.trim();
  if (!val) {
    titleError.value = 'Task title required';
    return;
  }

  // Allow letters, numbers, spaces and common punctuation
  const titlePattern = /^[a-zA-Z0-9\s\-._,!?()'":;@#$%&*+=\/]+$/;
  if (!titlePattern.test(val)) {
    titleError.value = 'Task title contains invalid characters';
    return;
  }

  await api.post('/tasks', {
    title: val,
    status: 'pending',
  });

  title.value = '';
  loadTasks(1);
};

const handleFilterChange = () => {
  loadTasks(1);
};

const updateStatus = async (task, status) => {
  await api.put(`/tasks/${task.id}`, {
    status,
  });
  loadTasks(currentPage.value);
};

const deleteTask = async (id) => {
  await api.delete(`/tasks/${id}`);
  loadTasks(currentPage.value);
};

const restoreTask = async (id) => {
  await api.post(`/tasks/${id}/restore`);
  loadTasks(currentPage.value);
};

const forceDeleteTask = async (id) => {
  await api.delete(`/tasks/${id}/force`);
  loadTasks(currentPage.value);
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    loadTasks(page);
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    goToPage(currentPage.value + 1);
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    goToPage(currentPage.value - 1);
  }
};

const getStatusColor = (status) => {
  switch(status) {
    case 'completed': return '#4caf50';
    case 'in_progress': return '#ff9800';
    case 'pending': return '#f44336';
    default: return '#999';
  }
};

onMounted(() => loadTasks());
</script>

<template>
  <div class="task-manager">
    <div class="container">
      <h1 class="title">Task Manager</h1>

      <!-- Create Task Section -->
      <div class="create-section">
        <div class="input-wrapper">
          <input 
            v-model="title" 
            placeholder="Enter new task title..." 
            class="task-input"
            :class="{ 'input-error': titleError }"
            @keyup.enter="createTask"
          />
          <span v-if="titleError" class="error-message">{{ titleError }}</span>
        </div>
        <select 
          v-model="viewFilter"
          @change="handleFilterChange"
          class="status-filter"
        >
          <option value="active">Active</option>
          <option value="trashed">Trashed</option>
          <option value="all">All (with trashed)</option>
        </select>

        <select 
          v-model="statusFilter" 
          @change="handleFilterChange"
          class="status-filter"
        >
          <option value="">All Tasks</option>
          <option value="pending">Pending</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
        </select>
        <button @click="createTask" class="btn btn-primary">Add Task</button>
      </div>

      <!-- Task List Section -->
      <div class="list-section">
        <h2 class="list-title">{{ tasks.length }} Tasks</h2>
        
        <div v-if="tasks.length === 0" class="empty-state">
          <p>No tasks yet. Create one to get started!</p>
        </div>

        <table v-else class="tasks-table">
          <thead>
            <tr>
              <th class="col-serial">#</th>
              <th class="col-title">Task Title</th>
              <th class="col-status">Status</th>
              <th class="col-action">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(task, index) in tasks" :key="task.id" class="task-row">
              <td class="col-serial">{{ index + 1 }}</td>
              <td class="col-title">{{ task.title }}</td>
              <td class="col-status">
                <select
                  :value="task.status"
                  @change="updateStatus(task, $event.target.value)"
                  class="status-select"
                  :style="{ borderLeftColor: getStatusColor(task.status) }"
                  :disabled="task.is_trashed"
                >
                  <option value="pending">Pending</option>
                  <option value="in_progress">In Progress</option>
                  <option value="completed">Completed</option>
                </select>
              </td>
              <td class="col-action">
                <template v-if="task.is_trashed">
                  <button @click="restoreTask(task.id)" class="btn btn-primary">Restore</button>
                  <button @click="forceDeleteTask(task.id)" class="btn btn-danger" style="margin-left:6px">Delete Permanently</button>
                </template>
                <template v-else>
                  <button @click="deleteTask(task.id)" class="btn btn-danger">Delete</button>
                </template>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination-section">
          <div class="pagination-info">
            Showing <strong>{{ tasks.length }}</strong> of <strong>{{ totalTasks }}</strong> tasks
            (Page <strong>{{ currentPage }}</strong> of <strong>{{ totalPages }}</strong>)
          </div>

          <div class="pagination-controls">
            <button 
              @click="prevPage" 
              :disabled="currentPage === 1"
              class="btn btn-pagination"
            >
              ← Previous
            </button>

            <div class="page-numbers">
              <button 
                v-for="page in totalPages" 
                :key="page"
                @click="goToPage(page)"
                :class="['page-btn', { active: page === currentPage }]"
              >
                {{ page }}
              </button>
            </div>

            <button 
              @click="nextPage" 
              :disabled="currentPage === totalPages"
              class="btn btn-pagination"
            >
              Next →
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.task-manager {
  height: auto;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 15px 10px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.container {
  max-width: 1000px;
  margin: 0 auto;
  background: white;
  border-radius: 10px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  padding: 15px 20px;
}

.title {
  font-size: 24px;
  color: #333;
  margin-bottom: 12px;
  text-align: center;
  font-weight: 700;
}

/* Create Section */
.create-section {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}

.input-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.task-input {
  padding: 8px 12px;
  border: 2px solid #e0e0e0;
  border-radius: 6px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.task-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.task-input.input-error {
  border-color: #f44336;
  background: rgba(244, 67, 54, 0.05);
}

.task-input.input-error:focus {
  border-color: #f44336;
  box-shadow: 0 0 0 2px rgba(244, 67, 54, 0.1);
}

.error-message {
  font-size: 12px;
  color: #f44336;
  margin-top: 4px;
  font-weight: 500;
}

.status-filter {
  padding: 8px 12px;
  border: 2px solid #e0e0e0;
  border-radius: 6px;
  font-size: 13px;
  background: white;
  color: #333;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 130px;
}

.status-filter:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.status-filter:hover {
  border-color: #667eea;
}

.btn {
  padding: 8px 18px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-primary:active {
  transform: scale(0.98);
}

.btn-danger {
  background: #f44336;
  color: white;
  padding: 6px 12px;
  font-size: 12px;
}

.btn-danger:hover {
  background: #d32f2f;
  box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
}

/* List Section */
.list-section {
  margin-top: 10px;
}

.list-title {
  font-size: 16px;
  color: #555;
  margin-bottom: 10px;
  font-weight: 600;
}

.empty-state {
  text-align: center;
  padding: 30px 20px;
  color: #999;
  font-size: 16px;
}

/* Table Styles */
.tasks-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  font-size: 13px;
  table-layout: fixed;
}

.tasks-table thead {
  background: #f5f5f5;
  border-bottom: 1px solid #ddd;
}

.tasks-table th {
  padding: 8px 10px;
  text-align: left;
  font-weight: 600;
  color: #555;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.tasks-table td {
  padding: 8px 10px;
  border-bottom: 1px solid #f0f0f0;
  color: #333;
  vertical-align: middle;
}

.task-row {
  transition: all 0.2s ease;
}

.task-row:hover {
  background: #fafafa;
}

.col-serial {
  width: 48px;
  text-align: center;
  font-weight: 700;
  color: #667eea;
  font-size: 12px;
}


.col-title {
  max-width: 540px;
  font-weight: 500;
  font-size: 13px;
  white-space: normal;
  word-break: break-word;
  overflow-wrap: anywhere;
}

.col-status {
  width: 160px;
}


.col-action {
  width: 180px;
  text-align: center;
}

/* Status Select */

.status-select {
  max-width: 160px;
  width: auto;
  padding: 6px 10px;
  border: 1px solid #ddd;
  border-left: 3px solid #f44336;
  border-radius: 4px;
  background: white;
  color: #333;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.status-select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.08);
}

/* Pagination Styles */
.pagination-section {
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid #f0f0f0;
}

.pagination-info {
  text-align: center;
  color: #666;
  font-size: 12px;
  margin-bottom: 10px;
}

.pagination-controls {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  flex-wrap: wrap;
}

.btn-pagination {
  padding: 6px 12px;
  background: #f5f5f5;
  border: 1px solid #ddd;
  border-radius: 4px;
  color: #333;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 12px;
}

.btn-pagination:hover:not(:disabled) {
  background: #667eea;
  border-color: #667eea;
  color: white;
}

.btn-pagination:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 4px;
  flex-wrap: wrap;
  justify-content: center;
}

.page-btn {
  min-width: 28px;
  width: 28px;
  height: 28px;
  border: 1px solid #ddd;
  background: white;
  color: #333;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 11px;
  padding: 0;
}

.page-btn:hover {
  border-color: #667eea;
  color: #667eea;
}

.page-btn.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-color: #667eea;
  color: white;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
  .container {
    padding: 20px;
  }

  .title {
    font-size: 24px;
  }

  .create-section {
    flex-direction: column;
  }

  .tasks-table th,
  .tasks-table td {
    padding: 12px 8px;
    font-size: 14px;
  }

  .col-title {
    max-width: 200px;
    word-break: break-word;
  }

  .status-select {
    font-size: 12px;
  }

  .btn {
    padding: 10px 16px;
    font-size: 14px;
  }
}
</style>
