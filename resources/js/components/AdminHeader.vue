<template>
  <div>
    <b-navbar toggleable="lg" type="dark" variant="primary">
      <router-link
        custom
        :to="{ name: 'admin' }"
        v-slot="{ navigate, isActive, href }"
      >
        <b-navbar-brand :href="href" @click="navigate" :active="isActive"
          >Exam Admin</b-navbar-brand
        >
      </router-link>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav class="ml-auto">
          <router-link
            custom
            :to="{ name: 'user-admin' }"
            v-slot="{ navigate, isActive, href }"
          >
            <b-nav-item @click="navigate" :href="href" :active="isActive"
              >Users</b-nav-item
            >
          </router-link>
          <router-link
            custom
            :to="{ name: 'products-admin' }"
            v-slot="{ navigate, isActive, href }"
          >
            <b-nav-item @click="navigate" :href="href" :active="isActive"
              >Products</b-nav-item
            >
          </router-link>
          <b-nav-item @click="logOut">Log Out</b-nav-item>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>
  </div>
</template>

<script>
export default {
  methods: {
    async logOut() {
      try {
        await this.$store.dispatch("destroyToken");
        this.$router.push({ name: "login" });
      } catch ({ response }) {
        this.$notify({
          group: "notify",
          type: "error",
          title: "Error",
          text: response.message,
        });
      }
    },
  },
};
</script>
