const StudentList = {
    name: 'StudentList', // Add explicit name
    template: `
        <v-card elevation="3">
            <v-card-title class="text-h5 secondary white--text">
                <v-icon left color="white">mdi-account-group</v-icon>
                Students List ({{ students.length }})
                <v-spacer></v-spacer>
                <v-btn
                    icon
                    color="white"
                    @click="$emit('refresh-students')"
                    :loading="loading"
                >
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </v-card-title>

            <v-card-text style="max-height: 700px; overflow-y: auto; padding: 16px;">
                <!-- Student Cards Grid -->
                <v-row v-if="students.length > 0">
                    <v-col 
                        v-for="student in students" 
                        :key="student.id" 
                        cols="12"
                        class="pa-2"
                    >
                        <v-card 
                            elevation="2" 
                            hover
                            class="student-card"
                            :class="getCardColor(student.id)"
                        >
                            <v-card-title class="pb-2">
                                <v-avatar color="primary" class="mr-3">
                                    <v-icon color="white">mdi-account</v-icon>
                                </v-avatar>
                                <div>
                                    <div class="text-h6 font-weight-bold">
                                        {{ student.name }}
                                    </div>
                                    <v-chip 
                                        size="small" 
                                        color="info" 
                                        variant="flat"
                                        class="mt-1"
                                    >
                                        <v-icon left size="small">mdi-book-education</v-icon>
                                        {{ student.major }}
                                    </v-chip>
                                </div>
                            </v-card-title>

                            <v-card-text class="pt-0">
                                <v-row no-gutters>
                                    <v-col cols="12" class="mb-2">
                                        <div class="d-flex align-center">
                                            <v-icon color="primary" class="mr-2">mdi-email</v-icon>
                                            <span class="text-body-2">
                                                <strong>Email:</strong> {{ student.email }}
                                            </span>
                                        </div>
                                    </v-col>
                                    <v-col cols="12" class="mb-2">
                                        <div class="d-flex align-start">
                                            <v-icon color="primary" class="mr-2 mt-1">mdi-map-marker</v-icon>
                                            <span class="text-body-2">
                                                <strong>Address:</strong> {{ student.address }}
                                            </span>
                                        </div>
                                    </v-col>
                                    <v-col cols="12" v-if="student.id">
                                        <div class="d-flex align-center">
                                            <v-icon color="primary" class="mr-2">mdi-identifier</v-icon>
                                            <span class="text-body-2 text-grey-darken-1">
                                                <strong>ID:</strong> #{{ student.id }}
                                            </span>
                                        </div>
                                    </v-col>
                                </v-row>
                            </v-card-text>

                            <v-card-actions class="pt-0">
                                <v-spacer></v-spacer>
                                <v-btn
                                    size="small"
                                    color="primary"
                                    variant="outlined"
                                    @click="$emit('view-student', student)"
                                    class="mr-2"
                                >
                                    <v-icon left size="small">mdi-eye</v-icon>
                                    View Details
                                </v-btn>
                                <v-btn
                                    size="small"
                                    color="secondary"
                                    variant="outlined"
                                    @click="$emit('edit-student', student)"
                                    class="mr-2"
                                >
                                    <v-icon left size="small">mdi-pencil</v-icon>
                                    Edit
                                </v-btn>
                                <v-btn
                                    size="small"
                                    color="error"
                                    variant="outlined"
                                    @click="$emit('delete-student', student)"
                                >
                                    <v-icon left size="small">mdi-delete</v-icon>
                                    Delete
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>

                <v-empty-state
                    v-else-if="!loading"
                    icon="mdi-account-off"
                    title="No Students Found"
                    text="Add your first student using the form on the left"
                ></v-empty-state>

                <div v-if="loading" class="text-center pa-4">
                    <v-progress-circular
                        indeterminate
                        color="primary"
                        size="50"
                    ></v-progress-circular>
                    <div class="mt-2 text-body-2">Loading students...</div>
                </div>
            </v-card-text>
        </v-card>
    `,

    props: {
        students: {
            type: Array,
            default: () => []
        },
        loading: {
            type: Boolean,
            default: false
        }
    },

    methods: {
        getCardColor(id) {
            const colors = ['', 'bg-blue-lighten-5', 'bg-green-lighten-5', 'bg-purple-lighten-5', 'bg-orange-lighten-5'];
            return colors[id % colors.length];
        }
    }
};