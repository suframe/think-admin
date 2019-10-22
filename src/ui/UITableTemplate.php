<el-table
        :data="tableData"
        border
        show-summary
        style="width: 100%">
    <?php foreach ($header as $key => $item) { ?>
        <el-table-column
                prop="<?= $key ?>"
                label="<?= $item ?>"
            <?php if (in_array($key, $sort)) { ?>
                sortable
            <?php } ?>
        >
            <?php if (isset($column[$key])) {
                switch ($column[$key]) {
                    case 'image':
                ?>
                <template slot-scope="scope">
                    <el-image
                            style="width: 80px; height: 80px"
                            :src="scope.row.<?= $key ?>"
                            :preview-src-list="[scope.row.<?= $key ?>]">
                    </el-image>
                </template>
                <?php
                        break;
                }
                ?>
            <?php } ?>
        </el-table-column>
    <?php } ?>
</el-table>

<?php
$i = 1;
$demoData = [];
for ($i = 0; $i < 10; $i++) {
    $demoData[] = [
        'id' => $i + 1,
        'username' => 'username' . $i,
        'avatar' => 'http://127.0.0.1:8000/storage/thinkAdmin/20191022/84c9e093d8668408b40e748ce3a018b7.png',
        'create_time' => '2019-10-22'
    ];
}
$demoData = json_encode($demoData);
?>

<!-- split -->

<script>
    new Vue({
        el: '#<?= $id ?>',
        template: "#<?= $id ?>Cnt",
        data: function () {
            return {
                tableData: []
            }
        },
        methods: {
            getList: function () {
                this.tableData = <?= $demoData ?>
            }
        },
        created: function () {
            this.getList()
        }
    })
</script>