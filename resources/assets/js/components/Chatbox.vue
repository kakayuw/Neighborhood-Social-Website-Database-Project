<template>
<el-container>
  <el-table style="maxHeight:100%" :data="myfriends">
    <el-table-column  label="Name"   width="180">
    <template slot-scope="scope">
        <el-popover trigger="hover" placement="left">
        <p>Email:    {{scope.row.email}}   </p>
        <p>Phone:    {{scope.row.phone}}   </p>
        <p>Desc.:    {{scope.row.description}}   </p>
        <div slot="reference" class="name-wrapper">
            <el-tag size="medium">  <i class="el-icon-user-solid"></i>&nbsp; {{scope.row.uname}}   </el-tag>
        </div>
        </el-popover>
    </template>
    </el-table-column>
    <el-table-column label="block/hood"  width="180">
        <template slot-scope="scope">
            <span style="margin-left: 10px">   {{scope.row.lname}}   </span>
        </template>
    </el-table-column>
    <el-table-column  label="Operations">
        <template slot-scope="scope">
            <el-button plain size="mini" type="warning" @click="openChatBox(scope.row.uid, scope.row.uname)">Message</el-button>
        </template>
    </el-table-column>
  </el-table>
  <el-drawer
    :visible.sync="drawer"
    :title="'chat to: '+friendname"
    direction="rtl"
    size="50%"
    :before-close="handleClose">
    <div class="mainchat">
    <div class="message" id="msg_container">
        <ul id="msgul">
            <li v-for="m in msgs" :key="m.mid">
                <p class="time">
                    <span>{{ m.mtimestamp }}</span>
                </p>
                <div class="main" :class="m.author==myid?'self':''">
                    <img class="avatar" width="30" height="30"/>
                    <div class="text">{{ m.title? m.title:'' + m.text?m.text:'' }}</div>
                </div>
            </li>
        </ul>
    </div>
    <div class="textbox">
        <textarea placeholder="Press Ctrl + Enter to send message" v-model="content" @keyup="onKeyup"></textarea>
    </div>
    </div>
  </el-drawer>
</el-container>
</template>

<script>
  import axios from 'axios'
  import JQuery from 'jquery'

  export default {
    props: ['messages', 'routeget',  'routesend', 'routefetch', 'showdrawer', 'friends', 'selfid'],
    data() {
      return {
        drawer: this.showdrawer,
        isCollapse: true,
        msgs: this.messages,
        ajaxurlget: this.routeget,
        ajaxurlsend: this.routesend,
        ajaxurlfetch: this.routefetch,
        myfriends: this.friends,
        content: '',
        friendid: -1,
        resp: '',
        test: 1234,
        threadid: -1,
        myid: this.selfid,
        friendname: '',
        timer: ''
      };
    },
    created () {
      this.fetchEventsList();
      this.timer = setInterval(this.fetchEventsList, 2000);
    },
    methods: {
      fetchEventsList(){
        var lastmid = this.msgs ? (this.msgs[this.msgs.length-1].mid) : -1;
        var self = this;
        console.log("check update: lastmid friendid, threadid",lastmid, this.friendid, this.threadid)
        console.log("myid", this.myid, "this.msg", this.msgs, " route", this.ajaxurlfetch)
        if (lastmid > 0 && this.threadid > 0 && this.friendid > 0) {
          $.ajax({
            url: this.ajaxurlfetch,
            data: { 
              fid: this.friendid,
              tid: this.threadid,
              mid: lastmid
            },
            type: "GET",
            dataType: "json",
            success:function (response) {
              console.log("update new msgs:", response.messages);
              response.messages.forEach(element => {
                self.msgs.push(element);
              });
              console.log("update new msgs:", response);
            }
          });
        }
      },
      handleClose(done) {
        this.$confirm('Are you sure you want to close this?')
          .then(_ => {
            done();
          })
          .catch(_ => {});
      },
      onKeyup (e) {
        if (e.ctrlKey && e.keyCode === 13 && this.content.length) {
          console.log("thismsg", this.msgs, this.threadid)
          var mid = null;
          var self = this;
          $.ajax({
            url: this.ajaxurlsend,
            data: { 
              fid: this.friendid,
              content: this.content,
            },
            type: "GET",
            dataType: "json",
            success:function (response) {
              mid = response.mid;
              console.log("send response:", response, " and mid is", mid);
              var currentdate = new Date(); 
              var datetime = currentdate.getFullYear() + "-" + (currentdate.getMonth()+1)  + "-" + currentdate.getDate() + " " + currentdate.getHours() + ":"  + currentdate.getMinutes() + ":" + currentdate.getSeconds();
              self.msgs.push({ "mid": mid, "thdid":  self.threadid, "author":  self.myid, "mtimestamp": datetime, "title": null, "text":  self.content, "address": null, "replyto": null })
              self.content = '';
            },
            finally:function (response) {
              self.content = '';
            }
          });
        }
      },
      openChatBox(uid, uname) {
        console.log(uid, " this.ajaxurl.get",uname);
        this.drawer = true;
        this.friendid = uid;
        this.friendname = uname;
        var self = this;
        $.ajax({
            url: this.ajaxurlget,
            data: { fid: uid},
            type: "GET",
            dataType: "json",
            success:function (response) {
              self.threadid = response.thdid;
              self.msgs = response.messages;
              console.log("response",response, "threadid:",response.thdid, "this.msgs", self.msgs);
            } ,
            error: function (response) {
              console.log("error", response)
            },
            finally: function(response) {
              console.log("finally", response)
            }
        });
      },
    },
    updated: function () {
      // keep the messege position down to the bottom
      document.getElementById('msg_container').scrollTop =  document.getElementById('msg_container').scrollHeight;
    },
    beforeDestroy () {
      clearInterval(this.timer)
    }
  } 

  function ajaxGet(id) {

    return resp
  }
</script>


<style lang="scss" scoped>
.DrawerTitle {
  height: 40px;
}

.mainchat {
    position: absolute;
    background-color: #eee;
    width: 100%;
    height: 100%;
}

.message {
    padding: 10px 15px;
    position: absolute;
    width: 100%;
    bottom: 240px;
    top: 0;
    left: 0;
    overflow-y: auto;
    height: 'calc(100% - 160px)';
    width: "100%";
    // position: absolute;
    li {
        margin-bottom: 15px;
        list-style-type: none;
    }
    .time {
        margin: 7px 0;
        text-align: center;
        > span {
            display: inline-block;
            padding: 0 18px;
            font-size: 12px;
            color: #fff;
            border-radius: 2px;
            background-color: #dcdcdc;
        }
    }
    .avatar {
        float: left;
        margin: 0 10px 0 0;
        border-radius: 3px;
    }
    .text {
        display: inline-block;
        position: relative;
        padding: 0 10px;
        max-width: 'calc(100% - 40px)';
        min-height: 30px;
        line-height: 2.5;
        font-size: 12px;
        text-align: left;
        word-break: break-all;
        background-color: #fcfcfc;
        border-radius: 4px;
        &:before {
            content: " ";
            position: absolute;
            top: 9px;
            right: 100%;
            border: 6px solid transparent;
            border-right-color: #fcfcfc;
        }
    }
    .self {
        text-align: right;
        .avatar {
            float: right;
            margin: 0 0 0 10px;
        }
        .text {
            background-color: #c6e6f5;
            &:before {
                right: inherit;
                left: 100%;
                border-right-color: transparent;
                border-left-color: #c6e6f5;
            }
        }
    }
}

.textbox {
    position: absolute;
    width: 100%;
    bottom: 0;
    left: 0;
    height: 240px;
    border-top: solid 1px #ddd;
    textarea {
        padding: 10px;
        height: 100%;
        width: 100%;
        border: none;
        outline: none;
        font-family: "Micrsofot Yahei";
        resize: none;
    }
}
</style>