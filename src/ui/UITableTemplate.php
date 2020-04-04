<el-row>
    <el-col :span="<?= $buttons ? 20 : 24 ?>">
        <?php
        $hasPickerOption = false;
        $cascader = [];
        if ($filter) {
            ?>
            <el-form :inline="true" :model="searchParam" ref="<?= $searchFormId ?>" class="demo-form-inline">
                <?php foreach ($filter as $key => $item) { ?>
                    <el-form-item label="<?= $item['label'] ?>">
                        <?php
                        switch ($item['type']) {
                            case 'select':
                                $multiple = isset($item['multiple']) && $item['multiple'] ? 'multiple' : null;
                                ?>
                                <el-select size="small" v-model="searchParam.<?= $key ?>" <?= $multiple ?>
                                           filterable
                                           placeholder="<?= $item['label'] ?>">
                                    <?php foreach ($item['value'] as $k => $v) { ?>
                                        <el-option label="<?= $v ?>" value="<?= $k ?>"></el-option>
                                    <?php } ?>
                                </el-select>
                                <?php
                                break;
                            case 'date':
                            case 'datetime':
                                $valueFormat = $item['type'] == 'date' ? 'yyyy-MM-dd' : 'yyyy-MM-dd HH:mm:ss';
                                ?>
                                <el-date-picker
                                        value-format="<?= $valueFormat ?>"
                                        size="small"
                                        v-model="searchParam.<?= $key ?>"
                                        type="<?= $item['type'] ?>"
                                        placeholder="<?= $item['label'] ?>">
                                </el-date-picker>
                                <?php
                                break;
                            case 'daterange':
                            case 'datetimerange':
                                $valueFormat = $item['type'] == 'daterange' ? 'yyyy-MM-dd' : 'yyyy-MM-dd HH:mm:ss';
                                $hasPickerOption = true;
                                ?>
                                <el-date-picker
                                        value-format="<?= $valueFormat ?>"
                                        size="small"
                                        v-model="searchParam.<?= $key ?>"
                                        type="<?= $item['type'] ?>"
                                        align="right"
                                        unlink-panels
                                        range-separator="至"
                                        start-placeholder="开始日期"
                                        end-placeholder="结束日期"
                                        :picker-options="pickerOptions">
                                </el-date-picker>
                                <?php
                                break;
                            case 'cascader':
                                $cascader[$key] = json_encode($item['value']);
                                $props = [
                                    'expandTrigger' => $item['expandTrigger'] ?? 'hover',
                                ];
                                isset($item['multiple']) && $item['multiple'] && ($props['multiple'] = $item['multiple']);
                                isset($item['checkStrictly']) && $item['checkStrictly'] && ($props['checkStrictly'] = $item['checkStrictly']);
                                $props = json_encode($props);
                                ?>
                                <el-cascader
                                        clearable
                                        :show-all-levels="false"
                                        :props='<?= $props ?>'
                                        v-model="searchParam.<?= $key ?>"
                                        :options="cascader_<?= $key ?>"
                                ></el-cascader>
                                <?php
                                break;
                            default:
                                ?>
                                <el-input size="small" clearable v-model="searchParam.<?= $key ?>"
                                          placeholder="<?= $item['label'] ?>"></el-input>
                                <?php
                                break;
                        }
                        ?>
                    </el-form-item>
                <?php } ?>

                <el-form-item>
                    <el-button size="small" type="primary" @click="onSubmit">搜索</el-button>
                    <el-button size="small" @click="resetForm('<?= $searchFormId ?>')">重置</el-button>
                </el-form-item>
            </el-form>
        <?php } ?>
        &nbsp;
    </el-col>
    <?php
    if ($buttons) {
        ?>
        <el-col style="margin-top: 4px" :span="4">
            <div class="el-form-item">
                <el-dropdown size="small" split-button type="primary" @command="handleCommand">
                    操作
                    <el-dropdown-menu slot="dropdown">
                        <?php foreach ($buttons as $key => $item) { ?>
                            <el-dropdown-item
                                    :command='<?= json_encode($item) ?>'><?= $item['title'] ?></el-dropdown-item>
                        <?php } ?>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
        </el-col>
        <?php
    }
    ?>
</el-row>
<?php if ($breadcrumb) { ?>
    <el-breadcrumb separator-class="el-icon-arrow-right" style="margin-bottom: 15px;">
        <?php foreach ($breadcrumb as $key => $item) { ?>
            <?php if (is_string($item)) { ?>
                <el-breadcrumb-item><?php echo $item ?></el-breadcrumb-item>
            <?php } else if (is_array($item)) { ?>
                <el-breadcrumb-item><a href="<?php echo $item[1] ?>"><?php echo $item[0] ?></a></el-breadcrumb-item>
            <?php } ?>
        <?php } ?>
    </el-breadcrumb>
<?php } ?>

<el-table
        :data="tableData"
        ref="thinkFilterTable<?= $id ?>"
        border
        stripe
        @sort-change="handleSort"
        @filter-change="handlerFilter"
        sortable="custom"
        style="width: 100%">
    <?php foreach ($header as $key => $item) { ?>
        <el-table-column
                prop="<?= $key ?>"
                column-key="<?= $column[$key]['field'] ?? $key ?>"
                label="<?= $item ?>"
            <?php if (isset($column[$key])) { ?>
                <?php
                $columnFilter = $column[$key]['filter'] ?? null;
                if ($columnFilter) {
                    $filterJson = [];
                    foreach ($columnFilter as $k => $v) {
                        $filterJson[] = ['text' => $v, 'value' => $k];
                    }
                    $filterJson = json_encode($filterJson);
                    ?>
                    :filters='<?= $filterJson ?>'
                    <?php
                    $filterMultiple = $column[$key]['multiple'] ?? null;
                    echo ':filter-multiple=' . ($filterMultiple ? 'true' : 'false');
                    ?>
                <?php } ?>

                <?php
                $columnFixed = $column[$key]['fixed'] ?? null;
                if ($columnFixed) {
                    echo 'fixed="' . $columnFixed . '"';
                }
                ?>

                <?php
                $columnWidth = $column[$key]['width'] ?? null;
                if ($columnWidth) {
                    echo 'width="' . $columnWidth . '"';
                }
                ?>

                <?php
                $columnSort = $column[$key]['sort'] ?? null;
                if ($columnSort) {
                    echo ':sortable="`custom`"';
                }
                ?>
            <?php } ?>
        >
            <?php if (isset($column[$key])) { ?>
                <?php
                $columType = $column[$key]['type'] ?? null;
                if ($columType) {
                    switch ($columType) {
                        case 'link':
                            ?>
                            <template slot-scope="scope">
                                <?php foreach ($column[$key]['linkConfig'] as $k => $v) {
                                    $filterValue = $v['value'] ?? null;
                                    if ($filterValue) {
                                        $filterValue = is_bool($filterValue) ? ($filterValue ? 'true' : 'false') : $filterValue;
                                        if (is_string($filterValue)) {
                                            $filterValue = "'{$filterValue}'";
                                        }
                                    }
                                    ?>
                                    <template <?php if ($filterValue !== null) { ?> v-if="scope.row.<?= $v['key'] ?>===<?= $filterValue ?>"<?php } ?>>
                                        <?php if (!(isset($v['showValue']) && ($v['showValue'] === false))) { ?>
                                            {{scope.row.<?= $key ?>}}
                                        <?php } ?>
                                        <?php if (isset($v['url'])) {
                                            $rowClick = __UITableBuildItemsUrl($v);
                                            ?>
                                            <el-button type="text" size="small"
                                                       @click='handleOps(scope.row, <?= $rowClick ?>)'>
                                                <?php if (isset($v['icon'])) { ?>
                                                    <i class="<?= $v['icon'] ?>"></i>
                                                <?php } ?>
                                                <?= $v['label'] ?>
                                            </el-button>
                                        <?php } ?>
                                    </template>
                                <?php } ?>
                            </template>
                            <?php
                            break;
                        case 'image':
                            ?>
                            <template slot-scope="scope">
                                <el-image
                                        v-if="scope.row.<?= $key ?>"
                                        style="width: 30px; height: 30px"
                                        :src="scope.row.<?= $key ?>"
                                        :preview-src-list="[scope.row.<?= $key ?>]">
                                </el-image>
                            </template>
                            <?php
                            break;
                        case 'images':
                            ?>
                            <template slot-scope="scope">
                                <el-image
                                        v-if="scope.row.<?= $key ?>"
                                        style="width: 30px; height: 30px"
                                        :src="scope.row.<?= $key ?>[0]"
                                        :preview-src-list="scope.row.<?= $key ?>"
                                >
                                </el-image>
                            </template>
                            <?php
                            break;
                    }
                }
                ?>

            <?php } ?>
        </el-table-column>
    <?php } ?>

    <?php if ($ops) { ?>
        <el-table-column
                fixed="right"
                label="操作"
                width="<?= $configs['opsWidth'] ?? 160 ?>">
            <template slot-scope="scope">
                <?php foreach ($ops as $key => $item) {
                    $rowClick = __UITableBuildItemsUrl($item);
                    ?>
                    <el-button type="text" size="small" @click='handleOps(scope.row, <?= $rowClick ?>)'>
                        <?php if (isset($item['icon'])) { ?>
                            <i class="<?= $item['icon'] ?>"></i>
                        <?php } ?>
                        <?= $item['label'] ?>
                    </el-button>
                <?php } ?>
            </template>
        </el-table-column>
    <?php } ?>
</el-table>

<el-pagination
        style="text-align: right;margin-top: 10px;"
        background
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        layout="total, sizes, prev, pager, next"
        :page-sizes="[10, 20, 50, 100]"
        :current-page="currentPage"
        :page-size="pageSize"
        :total="total">
</el-pagination>

<!-- split -->

<script>
    new Vue({
        el: '#<?= $id ?>',
        template: "#<?= $id ?>Cnt",
        data: function () {
            return {
                <?php
                if($hasPickerOption){
                ?>
                pickerOptions: {
                    shortcuts: [{
                        text: '最近一周',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近一个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近三个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        }
                    }]
                },
                <?php
                }
                ?>
                <?php
                foreach ($cascader as $key => $item) {
                ?>
                cascader_<?= '' . $key ?>: <?= $item ?>,
                <?php
                }
                ?>
                searchParam: {},
                total: 0,
                currentPage: 1,
                pageSize: 10,
                sk: {},
                tableData: []
            }
        },
        methods: {
            handleSort: function (params) {
                console.log(`排序了哦: `);
                console.log(params);
                this.searchParam.sort = params.prop;
                this.searchParam.sortType = params.order === 'ascending' ? 'asc' : 'desc';
                this.getList()
            },
            handleSizeChange: function (val) {
                this.currentPage = 1
                this.pageSize = val
                this.getList()
            },
            handleCurrentChange: function (val) {
                console.log(`跳转页数: ${val}`);
                this.currentPage = val
                this.getList()
            },
            handlerFilter(filters) {
                Object.assign(this.searchParam, filters)
                this.currentPage = 1;
                this.getList()
            },
            handleOps: function (row, config) {
                var _this = this
                if (config.confirm && !confirm(config.confirm)) {
                    return false
                }
                var vars = config.vars || []
                var tmpVar;
                if (config.type === 'link') {
                    for (var i in vars) {
                        tmpVar = vars[i];
                        if (tmpVar.indexOf('@') !== -1) {
                            tmpVar = tmpVar.split('@')
                            config.url = config.url.replace('__' + tmpVar[1] + '__', row[tmpVar[0]])
                        } else {
                            config.url = config.url.replace('__' + vars[i] + '__', row[vars[i]])
                        }
                    }
                    if (window.parent && config.blank) {
                        window.parent.postMessage(config);
                    } else {
                        window.location.href = config.url
                    }
                    return false
                }
                var params = {}
                for (var i in vars) {
                    if (row[vars[i]]) {
                        tmpVar = vars[i];
                        if (tmpVar.indexOf('@') !== -1) {
                            tmpVar = tmpVar.split('@')
                            params[tmpVar[1]] = row[tmpVar[0]]
                        } else {
                            params[vars[i]] = row[vars[i]]
                        }
                    }
                }
                if (config.type === 'ajax') {
                    $.post(config.url, params, function (rs) {
                        _this.$message({
                            showClose: true,
                            message: rs.message || (rs.code === 200 ? '操作成功' : '操作失败'),
                            type: rs.code === 200 ? 'success' : 'error'
                        });
                        _this.tableData = []
                        _this.getList()
                        if (config.callback) {
                            if (typeof (eval(config.callback)) == "function") {
                                config.callback(row, config, rs, _this);
                            }
                        }
                    }, 'json').fail(function (xhr, status, rs) {
                        _this.$message({
                            showClose: true,
                            message: xhr.responseJSON.message || '操作失败',
                            type: 'error'
                        });
                    })
                } else if (config.callback) {
                    if (typeof (eval(config.callback)) == "function") {
                        config.callback(row, config, params, _this);
                    }
                }
            },
            getList: function () {
                var params = {
                    page: this.currentPage,
                    pageSize: this.pageSize,
                    '_t': new Date().getTime()
                }
                Object.assign(params, this.searchParam)
                var _this = this
                $.getJSON('<?= $apiUrl ?>', params, function (rs) {
                    if (rs.code === 200) {
                        _this.tableData = rs.data.data
                        _this.total = rs.data.total
                    } else {
                        _this.$message({
                            showClose: true,
                            message: '无更多数据',
                            type: 'warring'
                        });
                    }
                })
            },
            onSubmit: function () {
                this.currentPage = 1;
                this.getList()
            },
            resetForm(formName) {
                this.$refs[formName].resetFields()
                this.searchParam = {}
                this.$refs['thinkFilterTable<?=$id?>'].clearFilter()
                this.currentPage = 1;
                this.getList()
            },
            handleCommand(command) {
                if (command.target) {
                    if (parent && parent.layer) {
                        var width = parent.document.body.clientWidth;
                        var height = parent.document.body.clientHeight - 60;
                        width = width > 1300 ? 1300 : (width - 70)
                        parent.layer.open({
                            type: 2,
                            title: command.title,
                            shadeClose: true,
                            shade: false,
                            maxmin: true, //开启最大化最小化按钮
                            area: [width + 'px', height + 'px'],
                            content: command.url,
                            zIndex: parent.layer.zIndex,
                            success: function (layero) {
                                parent.layer.setTop(layero); //重点2
                            }
                        });
                    } else {
                        window.open(command.url)
                    }
                } else if (command.isAjax) {
                    var _this = this
                    var params = {'_t': new Date().getTime()}
                    $.getJSON(command.url, function (rs) {
                        _this.$message({
                            showClose: true,
                            message: rs.message || (rs.code === 200 ? '操作成功' : '操作失败'),
                            type: rs.code === 200 ? 'success' : 'error'
                        });
                        _this.getList()
                    })
                } else {
                    window.location.href = command.url
                }
            }
        },
        created: function () {
            this.getList()
        }
    })
</script>