/*导出相册查看中的内容*/
function tableToExcel(json) {
    console.log(json);
    //列标题
    let str = `<tr>
                <th>排名</th>
                <th>姓名</th>
                <th>电话</th>
                <th>年级</th>
                <th>专业</th>
                
                <th>单位</th>
                <th>职务</th>
                
                <th>传递米数</th>
               </tr>`;
    //循环遍历，每行加入tr标签，每个单元格加td标签
    json.forEach(item => {
        str += '<tr>';
        str += `<td>${item.id}</td>`;
        str += `<td>${item.user.name}</td>`;
        str += `<td>${item.user.tle}</td>`;
        str += `<td>${item.user.grade}</td>`;
        str += `<td>${item.user.zhuanye}</td>`;

        str += `<td>${item.user.unit}</td>`;
        str += `<td>${item.user.Identity}</td>`;
        // str += `<td><span>&nbsp;</span>${item.ordernumlist}</td>`;

        str += `<td>${item.user.sum}</td>`;
        str += '</tr>';
    });
    let excelHtml = `
        <html>
            <head>
             <meta charset='utf-8' />
            </head>
             <body>
                <table>
                   ${str}
                </table>
             </body>
        </html>`;

    let blob = new Blob([excelHtml], {type: 'application/vnd.ms-excel'});

    // 创建一个a标签
    let oA = document.createElement('a');

    // 利用URL.createObjectURL()方法为a元素生成blob URL
    let url = URL.createObjectURL(blob);
    oA.href = url;
    // 给文件命名
    oA.download = `传递名单.xls`;
    // 模拟点击
    oA.click();
}

function formatZero(num, len) {
    if (String(num).length > len) return num;
    return (Array(len).join(0) + num).slice(-len);
}