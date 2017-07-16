<table class="table table-bordered">
    <tr>
        <th>id</th>
        <th>用户名</th>
        <th>姓名</th>
        <th>邮箱</th>
        <th>部门</th>
        <th style="width:60px;">状态</th>
    </tr>
    @foreach($lists as $list)
    <tr>
        <td>{{$list['id']}}</td>
        <td>{{$list['user_name']}}</td>
        <td>{{$list['real_name']}}</td>
        <td>{{$list['email']}}</td>
        <td>{{$list['dept_name']}}</td>
        <td>{{$status[$list['status']]}}</td>
    </tr>
    @endforeach
</table>