<template>
  <div>
    <Nav />
    <div class="container">
      <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
          <!-- Nested Row within Card Body -->
          <div class="row">
            <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
            <div class="col-lg-7">
              <div class="p-5">
                <div class="text-center">
                  <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                </div>
                <form class="user" @submit.prevent="register">
                  <div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                      <input
                        type="text"
                        class="form-control form-control-user"
                        id="exampleFirstName"
                        placeholder="First Name"
                        v-model="first_name"
                      />
                    </div>
                    <div class="col-sm-6">
                      <input
                        type="text"
                        class="form-control form-control-user"
                        id="exampleLastName"
                        placeholder="Last Name"
                        v-model="last_name"
                      />
                    </div>
                  </div>
                  <div class="form-group">
                    <input
                      type="email"
                      class="form-control form-control-user"
                      id="exampleInputEmail"
                      placeholder="Email Address"
                      v-model="email"
                    />
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                      <input
                        type="password"
                        class="form-control form-control-user"
                        id="exampleInputPassword"
                        placeholder="Password"
                        v-model="password"
                      />
                    </div>
                    <div class="col-sm-6">
                      <input
                        type="password"
                        class="form-control form-control-user"
                        id="exampleRepeatPassword"
                        placeholder="Repeat Password"
                        v-model="password_confirm"
                      />
                    </div>
                  </div>
                  <LoadingButton
                    text="Register Account"
                    v-bind:isLoading="isLoading"
                  />
                </form>
                <hr />
                <div class="text-center">
                  <router-link class="small" to="/forgot-password"
                    >Forgot Password?</router-link
                  >
                </div>
                <div class="text-center">
                  <router-link class="small" to="/login"
                    >Already have an account? Login!</router-link
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import * as notify from "../../utils/notify.js";
import Nav from "../../components/Nav.vue";
import LoadingButton from "../../components/LoadingButton.vue";
import { useToast } from "vue-toastification";

export default {
  name: "Register",
  components: {
    Nav,
    LoadingButton,
  },
  setup() {
    const toast = useToast();
    return { toast };
  },
  data() {
    return {
      first_name: "",
      last_name: "",
      email: "",
      password: "",
      password_confirm: "",
      isLoading: false,
    };
  },
  methods: {
    async register() {
      this.isLoading = true;
      try {
        var response = await axios.post("register", {
          first_name: this.first_name,
          last_name: this.last_name,
          email: this.email,
          password: this.password,
          password_confirm: this.password_confirm,
        });

        this.isLoading = false;

        if (response.data.must_verify_email) {
          this.$router.push(`/verify/user/${response.data.id}`);
        } else {
          let message =
            "Your account has been created successfully. Please Log in.";
          this.toast.success(message, {
            position: "top-right",
            timeout: 5000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: false,
            closeButton: "button",
            icon: true,
            rtl: false
          });
          this.$router.push("/login");
        }
      } catch (error) {
        notify.authError(error, this.toast);
        this.isLoading = false;
      }
    },
  },
};
</script>
