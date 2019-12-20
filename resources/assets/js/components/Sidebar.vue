<template>
    <el-aside  width="200px">
        <el-radio-group v-model="isCollapse" style="margin-bottom: 20px;">
            <el-radio-button :label="false">expand</el-radio-button>
            <el-radio-button :label="true">collapse</el-radio-button>
        </el-radio-group>
        <el-menu default-active="1-4-1" class="el-menu-vertical-demo" @open="handleOpen" @close="handleClose" :collapse="isCollapse">
        <el-menu-item index="1">
            <a href="/dbproj/public/home">
            <i class="el-icon-menu"></i>
            <span slot="title">Homepage</span></a>
        </el-menu-item>
        <el-submenu index="2">
            <template slot="title">
            <i class="el-icon-location"></i>
            <span slot="title">Community</span>
            </template>
            <el-menu-item index="2-1">
                <a href="/dbproj/public/community">
                <span slot="title">My Community</span></a>
            </el-menu-item>
            <el-menu-item index="2-2">
                <a href="/dbproj/public/community-join">
                <span slot="title">Join Community</span></a>
            </el-menu-item>
            <el-menu-item index="2-3">
                <a href="/dbproj/public/community-history">
                <span slot="title">My History</span></a>
            </el-menu-item>
        </el-submenu>
        <el-menu-item index="3">
            <a href="/dbproj/public/friend">
            <i class="el-icon-user"></i>
            <span slot="title">Relationship</span></a>
        </el-menu-item>
            <el-submenu index="4">
            <template slot="title">
            <i class="el-icon-message"></i>
            <span slot="title">Community</span>
            </template>
            <el-menu-item index="4-1">
                <a href="/dbproj/public/message">
                <span slot="title">Messages</span></a>
            </el-menu-item>
            <el-menu-item index="4-2">
                <a href="/dbproj/public/threads">
                <span slot="title">My Threads</span></a>
            </el-menu-item>
            <el-menu-item index="4-3">
                <a href="/dbproj/public/mapview">
                <span slot="title">Map View</span></a>
            </el-menu-item>
        </el-submenu>
        <el-menu-item index="5">
            <a href="/dbproj/public/profile">
            <i class="el-icon-setting"></i>
            <span slot="title">Settings</span></a>
        </el-menu-item>
        </el-menu>
    </el-aside>
</template> 

<style>
  .el-menu-vertical-demo:not(.el-menu--collapse) {
    width: 200px;
    min-height: 400px;
  }


</style>

<script>
  export default {
    data() {
      return {
        isCollapse: true
      };
    },
    methods: {
      handleOpen(key, keyPath) {
        console.log(key, keyPath);
      },
      handleClose(key, keyPath) {
        console.log(key, keyPath);
      }
    }
  }
</script>