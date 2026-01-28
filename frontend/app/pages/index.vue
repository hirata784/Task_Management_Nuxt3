<template>
    <div class="task">
        <header class="header">
            <h1>My Tasks</h1>
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
                <h2>タスク一覧</h2>
                <table class="list-table" border="1" rules="rows">
                    <tbody>
                        <tr>
                            <th></th>
                            <th>タスク名</th>
                            <th>ステータス</th>
                            <th>作成日</th>
                            <th>更新</th>
                            <th>削除</th>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr v-for="task in tasks" :key="task.id">
                            <td>
                                <input type="checkbox" v-model="task.is_done" />
                            </td>
                            <td>
                                <input
                                    type="text"
                                    class="task-name"
                                    v-model="task.title"
                                />
                            </td>
                            <td>
                                <label v-if="task.is_done">完了</label>
                                <label v-else>未完了</label>
                            </td>
                            <td>{{ task.created_at }}</td>
                            <td>
                                <button
                                    class="btn update"
                                    @click="taskUpdate(task)"
                                >
                                    更新
                                </button>
                            </td>
                            <td>
                                <button
                                    class="btn delete"
                                    @click="taskDelete(task)"
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

// 初期読み込み
const { data } = await useFetch("http://localhost/api/tasks");
tasks.value = data.value.data;
statusBoolean();

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
    statusBoolean();
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
    color: #fff;
}

h2 {
    margin: 0;
}

td {
    text-align: center;
}

.task {
    width: 90%;
    margin: 0 auto;
}

.header {
    background-color: #1f331f;
    padding: 10px;
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
    background-color: palegreen;
    width: 100%;
    padding: 10px;
}

.update {
    background-color: #fa98fa;
}

.delete {
    background-color: #fac998;
}

.btn {
    padding: 5px 15px;
    border: none;
    border-radius: 10px;
    color: #fff;
}
</style>
