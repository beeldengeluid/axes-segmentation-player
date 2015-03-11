var _input = {
 "ID": "123124asfwr234",
 "userID" : "dklsfjgbs",
 "description": "I want to find information on the Dutch royal family.",
 "relevant": [
    {
      "videoURL": "http://axes.ch.bbc.co.uk/collections/cAXES/videos/cAXES/v20080621_200000_bbctwo_andrew_marrs_history_of.webm",
      "title" : "The history of Andrew Marss",
      "keyframeURL": "",
      "start": 100000,
      "end": 120000,
    },
    {
      "videoURL": "http://axes.ch.bbc.co.uk/collections/cAXES/videos/cAXES/v20080516_100000_bbcone_to_buy_or_not_to_buy.webm",
      "title" : "To buy or not to buy",
      "keyframeURL": "",
      "start": 100000,
      "end": 200000,
    }
 ]
}

function post() {
	console.debug('posting to select.php');
    method = "post";
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", 'select.php');

	var hiddenField = document.createElement("input");
	hiddenField.setAttribute("type", "hidden");
	hiddenField.setAttribute("name", "data");
	hiddenField.setAttribute("value", JSON.stringify(_input));
	form.appendChild(hiddenField);

    document.body.appendChild(form);
    form.submit();
}