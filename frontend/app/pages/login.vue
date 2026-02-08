<template>
    <div class="task">
        <header class="header">
            <h1>My Tasks</h1>
        </header>
        <div class="form">
            <div class="form-inner">
                <h2>ログイン</h2>
                <div class="mail">
                    <p>メールアドレス</p>
                    <input
                        type="text"
                        class="txt"
                        v-model="mail"
                        placeholder="メールアドレスを入力"
                    />
                </div>
                <div class="password">
                    <p>パスワード</p>
                    <input
                        type="password"
                        class="txt"
                        v-model="password"
                        placeholder="パスワードを入力"
                    />
                </div>
                <div class="login">
                    <button class="login-btn" @click="login">ログイン</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const mail = ref("");
const password = ref("");

onMounted(() => {
    const token = localStorage.getItem("token");
    //  tokenがあれば、タスク管理画面へ戻る
    if (token != null) {
        navigateTo("/");
    }
});

// ログインボタン押下
async function login() {
    const res = await $fetch("http://localhost/api/auth/login", {
        method: "POST",
        body: {
            email: mail.value,
            password: password.value,
        },
    });
    // トークンを保存
    localStorage.setItem("token", res.access_token);
    // タスク一覧画面へ遷移
    navigateTo("/");
}
</script>

<style scoped>
h1 {
    margin: 0;
    color: #fff;
}

h2 {
    margin: 0;
}

p {
    margin: 0;
}

.task {
    width: 90%;
    margin: 0 auto;
}

.header {
    background-color: #1f331f;
    padding: 10px;
}

.form {
    background-color: palegreen;
    padding: 10px;
}

.form-inner {
    margin: 10px 0;
    padding: 30px;
    border: 1px solid #5d995d;
}

.mail {
    width: 100%;
    margin: 20px 0;
}

.password {
    width: 100%;
    margin: 20px 0;
}

.txt {
    width: 100%;
    padding: 5px;
    font-size: 20px;
    box-sizing: border-box;
    border: none;
    outline: none;
    background-color: palegreen;
    border-bottom: 1px solid #5d995d;
}

.login {
    display: flex;
    justify-content: center;
}

.login-btn {
    padding: 10px 15px;
    border: none;
    border-radius: 10px;
    color: #fff;
    background-color: #fa98fa;
    font-size: 20px;
    width: 60%;
    margin: 0 auto;
}
</style>
