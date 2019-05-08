var express = require('express')
var app = express()
const request = require('request');
const https = require('http');
const httpss = require('https');

app.get('/itemDetails', function (req, res) {
  var itemId=req.query.itemId;
  var path="http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=JSON&appid=MariaFer-Test-PRD-216de56dc-63f77791&siteid=0&version=967&ItemID="+itemId+"&IncludeSelector=Description,Details,ItemSpecifics";
  request(path, { json: true }, (err, response, body) => {
  if (err) { return console.log(err); }
  res.send(body);
  });
});

app.get('/photoDetails', function (req, res) {
  var title=req.query.title;
  var path='https://www.googleapis.com/customsearch/v1?q='
  path=path+title;
  path=path+'&cx=011468513572652619133:f20jjhcgxa4&imgSi%20ze=huge&num=8&searchType=image&key=AIzaSyCyW_12hh1WF02ZH6JM8gWSM9LQ0ZhEwtA';
  request(path, { json: true }, (err, response, body) => {
  if (err) { return console.log(err); }
  res.send(body);
  });
});

app.get('/similarDetails', function (req, res) {
  var itemId=req.query.itemId;
  var path='http://svcs.ebay.com/MerchandisingService?OPERATION-NAME=getSimilarItems&SERVICE-NAME=MerchandisingService&SERVICE-VERSION=1.1.0&CONSUMER-ID=MariaFer-Test-PRD-216de56dc-63f77791&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&itemId='
  path=path+itemId;
  path=path+'&maxResults=20';
  request(path, (err, response, body) => {
  if (err) { return console.log(err); }
  res.send(body);
  });
});

app.get('/postalCode', function (req, res) {
  var startsWith=req.query.startsWith;
  path="http://api.geonames.org/postalCodeSearchJSON?postalcode_startsWith="+startsWith+"&username=mariafernandes&country=US&maxRows=5";
  request(path, { json: true }, (err, response, body) => {
  if (err) { return console.log(err); }
  res.send(body);
  });
});

app.get('/', function (req, res) {
  var path=req.query.key;
  request(path, { json: true }, (err, response, body) => {
  if (err) { return console.log(err); }
  res.send(body);
  });
});

app.get('/csci571hw8.html', function (req, res) {
  res.sendfile(__dirname+"/"+"csci571hw8.html");
});

app.listen(8081);
