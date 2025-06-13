const { createApp } = Vue;
const { createVuetify } = Vuetify;

// Create Vuetify instance
const vuetify = createVuetify({
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                colors: {
                    primary: '#1976D2',
                    secondary: '#424242',
                    accent: '#82B1FF',
                    error: '#FF5252',
                    info: '#2196F3',
                    success: '#4CAF50',
                    warning: '#FFC107',
                }
            }
        }
    }
});

// Vue App
const app = createApp({
    components: {
        AddStudent: typeof AddStudent !== 'undefined' ? AddStudent : null,
        StudentList: typeof StudentList !== 'undefined' ? StudentList : null
    },

    data() {
        return {
            componentsLoaded: typeof AddStudent !== 'undefined' && typeof StudentList !== 'undefined',
            valid: false,
            loading: false,
            deleting: false,
            updating: false,
            snackbar: false,
            message: '',
            messageType: 'success',
            deleteDialog: false,
            detailsDialog: false,
            isEditMode: false,
            studentToDelete: null,
            selectedStudent: null,
            editingStudent: {
                id: null,
                name: '',
                major: '',
                email: '',
                address: ''
            },
            students: [],
            nameRules: [
                v => !!v || 'Name is required',
                v => (v && v.length >= 2) || 'Name must be at least 2 characters',
            ],
            majorRules: [
                v => !!v || 'Major is required',
                v => (v && v.length >= 2) || 'Major must be at least 2 characters',
            ],
            emailRules: [
                v => !!v || 'Email is required',
                v => /.+@.+\..+/.test(v) || 'Email must be valid',
            ],
            addressRules: [
                v => !!v || 'Address is required',
                v => (v && v.length >= 5) || 'Address must be at least 5 characters',
            ],
        }
    },

    methods: {
        async loadStudents() {
            this.loading = true;
            try {
                const response = await axios.get('api.php');
                this.students = Array.isArray(response.data) ? response.data : [];
            } catch (error) {
                console.error('Load students error:', error);
                this.showMessage('Failed to load students', 'error');
                this.students = [];
            } finally {
                this.loading = false;
            }
        },

        showMessage(message, type = 'success') {
            this.message = message;
            this.messageType = type;
            this.snackbar = true;
        },

        // Event handlers for AddStudent component
        async onStudentAdded(message) {
            this.showMessage(message, 'success');
            await this.loadStudents();
        },

        // Event handlers for StudentList component
        viewStudent(student) {
            this.selectedStudent = student;
            this.isEditMode = false;
            this.detailsDialog = true;
        },

        editStudent(student) {
            this.selectedStudent = student;
            this.editingStudent = {
                id: student.id,
                name: student.name,
                major: student.major,
                email: student.email,
                address: student.address
            };
            this.isEditMode = true;
            this.detailsDialog = true;
        },

        deleteStudent(student) {
            this.studentToDelete = student;
            this.deleteDialog = true;
        },

        async updateStudent() {
            if (!this.$refs.editForm.validate()) {
                return;
            }

            this.updating = true;
            try {
                const response = await axios.put('api.php', this.editingStudent);
                
                if (response.data.success) {
                    this.showMessage(response.data.message, 'success');
                    this.detailsDialog = false;
                    this.isEditMode = false;
                    await this.loadStudents();
                } else {
                    this.showMessage(response.data.error || 'Failed to update student', 'error');
                }
            } catch (error) {
                console.error('Update student error:', error);
                this.showMessage(error.response?.data?.error || 'An error occurred while updating student', 'error');
            } finally {
                this.updating = false;
            }
        },

        cancelEdit() {
            this.detailsDialog = false;
            this.isEditMode = false;
            this.selectedStudent = null;
            this.editingStudent = {
                id: null,
                name: '',
                major: '',
                email: '',
                address: ''
            };
        },

        closeDetailsDialog() {
            this.detailsDialog = false;
            this.isEditMode = false;
            this.selectedStudent = null;
            this.editingStudent = {
                id: null,
                name: '',
                major: '',
                email: '',
                address: ''
            };
        },

        async confirmDelete() {
            if (!this.studentToDelete || !this.studentToDelete.id) {
                this.showMessage('Invalid student selected for deletion', 'error');
                this.deleteDialog = false;
                return;
            }

            this.deleting = true;
            try {
                const response = await axios.delete(`api.php?id=${this.studentToDelete.id}`);
                
                if (response.data.success) {
                    this.showMessage(response.data.message || 'Student deleted successfully', 'success');
                    this.students = this.students.filter(s => s.id !== this.studentToDelete.id);
                    await this.loadStudents();
                } else {
                    this.showMessage(response.data.error || 'Failed to delete student', 'error');
                }
            } catch (error) {
                console.error('Delete student error:', error);
                this.showMessage(error.response?.data?.error || 'An error occurred while deleting student', 'error');
            } finally {
                this.deleting = false;
                this.deleteDialog = false;
                this.studentToDelete = null;
            }
        }
    },
});

app.use(vuetify).mount('#app');
console.log('Vue app initialized');