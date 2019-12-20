<template>
<el-container>
   <el-form :inline="true" :model="formInline" class="demo-form-inline half">
  <el-form-item label="Search">
    <el-input v-model="formInline.pattern" placeholder="any message content"></el-input>
  </el-form-item>
  <el-form-item label="Type">
    <el-select v-model="formInline.type" placeholder="Search Type">
      <el-option label="Message" value="message"></el-option>
      <!-- <el-option label="User" value="user"></el-option> -->
    </el-select>
  </el-form-item>
  <el-form-item>
    <el-button type="primary" @click="onSubmit">Query</el-button>
  </el-form-item>
  <el-form-item>
    <p v-if="type='message'">Show all messages containing words including chat messages, broadcasts, group messages and locality notification</p>
  </el-form-item>
</el-form>
  <el-card  class="fixoverflow fullwidth" header="Results:">
    <div v-if="formInline.type='message'">
    <el-card v-for="m in results" :key="m.mtimestamp" >
        <h4>{{m.title}}</h4>
        <p>{{m.text}}</p>
        <p><font color="#606266">{{m.mid}}&nbsp;@&nbsp;{{m.mtimestamp}}</font></p>
    </el-card>
    </div>
    <!-- <div v-if="formInline.type='user'">
    <el-card v-for="m in results" :key="m.id" >
        <h4>{{m.name}}</h4>
        <p>{{m.email}}</p>
        <p><font color="#606266"></font>{{m.rela}}</p>
    </el-card>
    </div> -->
  </el-card>
</el-container>
</template>

<script>
  import JQuery from 'jquery'
  export default {
    props: [],
    data() {
      return {
        formInline: {
          pattern: '',
          type: '',
        },
        results: null,
      };
    },
    methods: {
      onSubmit() {
        var self = this;
        if (this.formInline.pattern == '') return;
        $.ajax({
          url: '/dbproj/public/search',
          data: {
            text: self.formInline.pattern,
            type: self.formInline.type?self.formInline.type:'message'
          },
          type: "GET",
          dataType: "json",
          success:function (response) {
              self.results = response.result;
            }
        }); 
      }
    },
  } 
</script>

<style lang='scss' scoped>
.centerform {
  margin-left: 10%;
  width: 80%;
}

.fixoverflow{
    display:block;
    width:100%;
    height:450px;
    background-color:#EBEEF5;
    overflow-y: scroll;
}

.half {
  width: 30%;
}
</style>