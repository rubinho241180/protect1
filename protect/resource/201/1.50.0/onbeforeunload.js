
function unload2() {
	alert('umn');
	 //return "Mensagem de fechamento de janela....";
}

//window.onbeforeunload=unload2;


window.onbeforeunload = function(Event)
{
console.log(Event);

Event.preventDefault();
Event.stopImmediatePropagation();
Event.stopPropagation();

//open thickbox

}