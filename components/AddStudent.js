const AddStudent = {
    name: 'AddStudent', // Add explicit name
    template: `
        <v-card elevation="3">
            <v-card-title class="text-h5 primary white--text">
                <v-icon left color="white">mdi-account-plus</v-icon>
                Add New Student
            </v-card-title>
            
            <v-card-text>
                <v-form ref="form" v-model="valid" @submit.prevent="addStudent">
                    <v-text-field
                        v-model="student.name"
                        :rules="nameRules"
                        label="Full Name"
                        prepend-icon="mdi-account"
                        required
                        :disabled="loading"
                        variant="outlined"
                    ></v-text-field>

                    <v-text-field
                        v-model="student.major"
                        :rules="majorRules"
                        label="Major"
                        prepend-icon="mdi-book-education"
                        required
                        :disabled="loading"
                        variant="outlined"
                    ></v-text-field>

                    <v-text-field
                        v-model="student.email"
                        :rules="emailRules"
                        label="Email Address"
                        prepend-icon="mdi-email"
                        type="email"
                        required
                        :disabled="loading"
                        variant="outlined"
                    ></v-text-field>

                    <v-textarea
                        v-model="student.address"
                        :rules="addressRules"
                        label="Address"
                        prepend-icon="mdi-map-marker"
                        required
                        :disabled="loading"
                        variant="outlined"
                        rows="3"
                    ></v-textarea>
                </v-form>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="secondary"
                    variant="outlined"
                    @click="resetForm"
                    :disabled="loading"
                >
                    <v-icon left>mdi-refresh</v-icon>
                    Reset
                </v-btn>
                <v-btn
                    color="primary"
                    @click="addStudent"
                    :loading="loading"
                    :disabled="!valid"
                >
                    <v-icon left>mdi-content-save</v-icon>
                    Add Student
                </v-btn>
            </v-card-actions>
        </v-card>
    `,
    
    props: {
        loading: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            valid: false,
            student: {
                name: '',
                major: '',
                email: '',
                address: ''
            },
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
        async addStudent() {
            if (!this.valid) return;

            try {
                const response = await axios.post('api.php', this.student);
                
                if (response.data.success) {
                    this.$emit('student-added', response.data.message);
                    this.resetForm();
                } else {
                    this.$emit('show-message', response.data.error || 'Failed to add student', 'error');
                }
            } catch (error) {
                console.error('Add student error:', error);
                this.$emit('show-message', error.response?.data?.error || 'An error occurred while adding student', 'error');
            }
        },

        resetForm() {
            this.student = {
                name: '',
                major: '',
                email: '',
                address: ''
            };
            this.$refs.form?.resetValidation();
        }
    }
};