function getDataFromString(string,data){
    let objDate= new Date(string);
    switch (data){
        case 'fulldate':
            return objDate.getUTCFullYear()+'-'+String(objDate.getMonth()+1).padStart(2,'0')+'-'+String(objDate.getDate()).padStart(2,'0');
        case 'time':
            return String(objDate.getUTCHours()).padStart(2,'0')+':00';
        case 'timetoDefault':
            return String(objDate.getUTCHours()+1).padStart(2,'0')+':00';
    }

}
