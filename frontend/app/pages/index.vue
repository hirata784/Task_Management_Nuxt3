<template>
    <div class="task">
        <header class="header">
            <h1>My Tasks</h1>
            <button v-if="isLoggedIn" class="logoutBtn" @click="logout">
                ログアウト
            </button>
        </header>
        <div class="myTasks">
            <div class="input">
                <h2>入力フォーム</h2>
                <input type="text" class="txt" v-model="taskValue" />
                <button class="btn add" @click="taskAdd" :disabled="isLoading">
                    {{ isLoading ? "追加中…" : "追加" }}
                </button>
                <div v-if="addError" style="color: red">{{ addError }}</div>
                <div v-else-if="addSuccess" style="color: green">
                    {{ addSuccess }}
                </div>
            </div>
            <div class="list">
                <h2>{{ user.name }}さんのタスク一覧</h2>
                <table class="list-table" border="1" rules="rows">
                    <tbody>
                        <tr>
                            <th></th>
                            <th>タスク名</th>
                            <th>ステータス</th>
                            <th>作成日</th>
                            <th>編集</th>
                            <th>削除</th>
                        </tr>
                    </tbody>
                    <tbody>
                        <!-- 編集ボタンを押すと、背景色が変更。 -->
                        <tr
                            v-for="task in tasks"
                            :key="task.id"
                            :class="editFlag == task.id ? 'yellow' : ''"
                        >
                            <!-- 編集ボタンを押すと、チェックボックス操作可能になる。 -->
                            <td>
                                <input
                                    type="checkbox"
                                    v-model="task.is_done"
                                    v-if="editFlag == task.id"
                                />
                                <input
                                    type="checkbox"
                                    v-model="task.is_done"
                                    disabled="none"
                                    v-else
                                />
                            </td>
                            <!-- 編集ボタンを押すと、入力可能になる。 -->
                            <td>
                                <input
                                    type="text"
                                    class="task-name yellow"
                                    v-model="task.title"
                                    v-if="editFlag == task.id"
                                />
                                <input
                                    type="text"
                                    class="task-name green"
                                    v-model="task.title"
                                    readonly
                                    v-else
                                />
                            </td>
                            <td>
                                <label v-if="task.is_done">完了</label>
                                <label v-else>未完了</label>
                            </td>
                            <td>{{ task.created_at }}</td>
                            <!-- 編集ボタンを押すと、ボタンの表示が変更。 -->
                            <td>
                                <button
                                    class="btn update"
                                    @click="taskUpdate(task)"
                                    v-if="editFlag == task.id"
                                >
                                    更新
                                </button>
                                <button
                                    class="btn edit"
                                    @click="taskEdit(task)"
                                    v-else
                                >
                                    編集
                                </button>
                            </td>
                            <td>
                                <button
                                    class="btn cancel"
                                    @click="taskCancel(task)"
                                    v-if="editFlag == task.id"
                                >
                                    取消
                                </button>
                                <button
                                    class="btn delete"
                                    @click="taskDelete(task)"
                                    v-else
                                >
                                    削除
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="updateError" style="color: red">
                    {{ updateError }}
                </div>
                <div v-else-if="updateSuccess" style="color: green">
                    {{ updateSuccess }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const tasks = ref([]);
const taskValue = ref("");
const addError = ref("");
const updateError = ref("");
const isLoading = ref(false);
const addSuccess = ref("");
const updateSuccess = ref("");
const timer = ref(null);
const editFlag = ref("");
const taskBk = ref([]);
const isLoggedIn = ref(false);
const user = ref({});

onMounted(async () => {
    const token = localStorage.getItem("token");
    // tokenがあればtrue, なければfalse
    isLoggedIn.value = !!token;
    //  tokenがなければ、ログイン画面へ戻る
    if (token == null) {
        navigateTo("/login");
    }

    try {
        // ユーザー取得
        const userRes = await $fetch("http://localhost/api/auth/user", {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        user.value = userRes;

        // タスク取得
        const taskRes = await $fetch("http://localhost/api/tasks", {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        tasks.value = taskRes.data;
        statusBoolean();
    } catch (error) {
        alert("ログインユーザーの取得に失敗しました。");
    }
});

// 追加
async function taskAdd() {
    // 空白orスペースのみの場合、処理しない
    if (taskValue.value.trim() == "") {
        addError.value = "タスク名を入力してください";
        taskValue.value = "";
        addSuccess.value = "";
        return;
    }

    isLoading.value = true;

    try {
        const res = await $fetch("http://localhost/api/tasks/", {
            method: "POST",
            body: {
                title: taskValue.value,
                is_done: false,
            },
        });
        // データ変更後すぐ反映
        tasks.value = res.data;
        statusBoolean();
        // エラー表示を削除
        taskValue.value = "";
        addError.value = "";
        // 成功メッセージを表示
        addSuccess.value = "タスクの追加に成功しました";
        // バックアップを更新する
        taskBk.value = JSON.parse(JSON.stringify(tasks.value));
        // すでにタイマーがある場合削除
        if (timer.value) {
            clearTimeout(timer.value);
        }
        timer.value = setTimeout(() => {
            // 3秒後にメッセージを破棄
            addSuccess.value = "";
            timer.value = null;
        }, 3000);
    } catch (e) {
        addError.value = "タスクの追加に失敗しました";
    } finally {
        isLoading.value = false;
    }
}

// 編集
async function taskEdit(task) {
    // 最新バックアップを取得する
    taskBk.value = JSON.parse(JSON.stringify(tasks.value));
    // 編集ボタンを押した行に更新ボタンを表示
    editFlag.value = task.id;
}

// 更新
async function taskUpdate(task) {
    // 空白orスペースのみの場合、処理しない
    if (task.title.trim() === "") {
        updateError.value = "タスク名を入力してください";
        task.title = "";
        updateSuccess.value = "";
        return;
    }

    try {
        const res = await $fetch("http://localhost/api/tasks/" + task.id, {
            method: "PUT",
            body: {
                title: task.title,
                is_done: task.is_done,
            },
        });
        // データ変更後すぐ反映
        tasks.value = res.data;
        statusBoolean();
        // エラー表示を削除
        updateError.value = "";
        // 成功メッセージを表示
        updateSuccess.value = "タスクの更新に成功しました";
        // 編集モードを解除
        editFlag.value = "";
        // バックアップを更新する
        taskBk.value = JSON.parse(JSON.stringify(tasks.value));
        // すでにタイマーがある場合削除
        if (timer.value) {
            clearTimeout(timer.value);
        }
        timer.value = setTimeout(() => {
            // 3秒後にメッセージを破棄
            updateSuccess.value = "";
            timer.value = null;
        }, 3000);
    } catch (e) {
        updateError.value = "タスクの更新に失敗しました";
    }
}

// 削除
async function taskDelete(task) {
    const res = await $fetch("http://localhost/api/tasks/" + task.id, {
        method: "DELETE",
    });
    // データ変更後すぐ反映
    tasks.value = res.data;
    // バックアップを更新する
    taskBk.value = JSON.parse(JSON.stringify(tasks.value));
    statusBoolean();
}

// 取消
async function taskCancel(task) {
    // 編集モードを解除
    editFlag.value = "";
    // 変更前の状態に戻す
    tasks.value = taskBk.value;
}

// ログアウト
async function logout() {
    const token = localStorage.getItem("token");
    // すでにログアウト済みの場合、処理しない
    if (!token) {
        isLoggedIn.value = false;
        return;
    }

    try {
        const res = await $fetch("http://localhost/api/auth/logout", {
            method: "POST",
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        localStorage.removeItem("token");
        isLoggedIn.value = false;
        // ログイン画面へ遷移
        navigateTo("/login");
    } catch (e) {
        alert("ログアウトに失敗しました。");
    }
}

// ステータスのintをbooleanに変換
function statusBoolean() {
    for (const task of tasks.value) {
        if (task.is_done == 1) {
            // tasksテーブル：is_done = 1 → true
            task.is_done = true;
        } else if (task.is_done == 0) {
            // tasksテーブル：is_done = 0 → false
            task.is_done = false;
        }
    }
}
</script>

<style scoped>
h1 {
    margin: 0;
}

h2 {
    margin: 0;
}

.yellow {
    background-color: lightyellow;
}

.green {
    background-color: palegreen;
}

td {
    text-align: center;
}

.task {
    width: 90%;
    margin: 0 auto;
}

.header {
    display: flex;
    justify-content: space-between;
    background-color: #1f331f;
    color: #fff;
    padding: 10px;
}

.logoutBtn {
    color: #fff;
    background-color: #1f331f;
    border: none;
    font-size: 18px;
}

.myTasks {
    background-color: palegreen;
    padding: 10px;
}

.input {
    margin: 10px 0;
    padding: 10px;
    border: 1px solid #5d995d;
}

.txt {
    width: 30%;
}

.add {
    background-color: #98c9fa;
    margin-left: 10px;
}

.list {
    margin: 10px 0;
    padding: 10px;
    border: 1px solid #5d995d;
}

.list-table {
    border-collapse: collapse;
    width: 100%;
}

.task-name {
    border: none;
    outline: none;
    width: 100%;
    padding: 10px;
}

.update {
    background-color: #fa98fa;
}

.edit {
    background-color: #9898fa;
}

.delete {
    background-color: #fac998;
}

.cancel {
    background-color: red;
}

.btn {
    padding: 5px 15px;
    border: none;
    border-radius: 10px;
    color: #fff;
}
</style>
