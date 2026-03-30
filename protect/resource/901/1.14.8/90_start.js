/*
console.log('will start Stream');
try {
    WAPP.monitoringStream();
}
catch(err) {
    console.log('error1: '+err.message);
}
console.log('will start Stream: OK');
*/

console.log('will start Status 03/11/2020');
try {

    //setTimeout(function() {
        
        WAPP.monitoringStatus();

    //}, 3000);
}
catch(err) {
    console.log('error1: '+err.message);
}
console.log('will start Status: OK');


//console.log('will start Messages');
//try {
    //WAPP.monitoringMessages();
//}
//catch(err) {
//    console.log('error2: '+err.message);
//}
//console.log('will start Messages: OK');



console.log('---=> 90_start.js');
