<template>
    <div class="task">
        <header class="header">
            <h1>My Tasks</h1>
        </header>
        <div class="myTasks">
            <div class="input">
                <h2>入力フォーム</h2>
                <input type="text" class="txt" />
                <button class="btn add">追加</button>
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
                            <td>{{ task.title }}</td>
                            <td>
                                <label v-if="task.is_done">完了</label>
                                <label v-else>未完了</label>
                            </td>
                            <td>2026/1/11</td>
                            <td>
                                <button
                                    class="btn update"
                                    @click="taskUpdate(task)"
                                >
                                    更新
                                </button>
                            </td>
                            <td>
                                <button class="btn delete">削除</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
const tasks = ref([]);

// 初期読み込み
const { data } = await useFetch("http://localhost/api/tasks");
tasks.value = data.value.data;

// ステータスのintをbooleanに変換
for (const task of tasks.value) {
    if (task.is_done == 1) {
        // tasksテーブル：is_done = 1 → true
        task.is_done = true;
    } else if (task.is_done == 0) {
        // tasksテーブル：is_done = 0 → false
        task.is_done = false;
    }
}

// 更新
async function taskUpdate(task) {
    const res = await $fetch("http://localhost/api/tasks/" + task.id, {
        method: "PUT",
        body: {
            title: task.title,
            is_done: task.is_done,
        },
    });
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
