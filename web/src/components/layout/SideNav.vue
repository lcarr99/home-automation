<template>
  <div id="side-nav">
    <aside>
      <article>
        <nav>
          <ul>
            <li>
              <router-link
                :to="{
                  name: 'home',
                }"
                activeClass="active"
                >Home</router-link
              >
            </li>
            <li><a href="#">Utilities</a></li>
            <li><a href="#">Budget</a></li>
            <li><a href="#">Logout</a></li>
          </ul>
        </nav>
      </article>
    </aside>
  </div>
</template>

<script setup>
import { RouterLink } from "vue-router";
</script>

<style scoped></style>
