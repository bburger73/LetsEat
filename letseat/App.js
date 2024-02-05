import React from 'react';
import {Text,Platform, SafeAreaView } from "react-native";

import * as Linking from 'expo-linking';

import * as Notifications from 'expo-notifications';
import Constants from 'expo-constants';
import { LinkingContext, NavigationContainer } from '@react-navigation/native';
import { ToastProvider } from "react-native-toast-notifications";
import Forgot from './Forgot';
import Recover from './Recover';
import Settings from './Settings';
import Signin from './Signin';
import Signup from './Signup';
import UserDash from './UserDashboard';
import CreateRestaurant from './CreateRestaurant';
import CreatePoll from './CreatePoll';
import CreateFeastGroup from './CreateFeastGroup';
import CreateRestaurantGroup from './CreateRestaurantGroup';
import ManageRestaurantGroup from './ManageRestaurantGroup';
import UpdateRestaurantGroup from './UpdateRestarauntGroup';
import ManageFeastGroup from './ManageFeastGroup';
import UpdateFeastGroup from './UpdateFeastGroup';
import ManagePolls from './ManagePolls';
import UpdatePoll from './UpdatePoll';
import ManageVotes from './ManageVotes';
import Vote from './Vote';
import ManageRestaurants from './ManageRestaurants';
import UpdateRestaurant from './UpdateRestaurant';
import Results from './Results';
import VoteResults from './VoteResults';

if(Platform.OS === 'ios' || Platform.OS === 'android')
{
    Notifications.setNotificationHandler({
        handleNotification: async () => ({
            shouldShowAlert: true,
            shouldPlaySound: false,
            shouldSetBadge: false,
        }),
    });
    
    componentDidMount = () => {
        registerForPushNotificationsAsync();
    
        Notifications.addNotificationReceivedListener(this._handleNotification);
    
        Notifications.addNotificationResponseReceivedListener(this._handleNotificationResponse);
    }
    
    _handleNotification = notification => {
        this.setState({ notification: notification });
    };
    
    _handleNotificationResponse = response => {
    
    };    
}

export default function App() {
    const [timer,setTimer] = React.useState(null)
    const [user, signin] = React.useState({});
    const [page, setpage] = React.useState();

    const [expoPushToken, setExpoPushToken] = React.useState('');
    const [notification, setNotification] = React.useState(false);
    const notificationListener = React.useRef();
    const responseListener = React.useRef();
    const [forgot, setForgot] = React.useState({});
    const [data,setData] = React.useState(null);

    const [restaurant,setRestaurant] = React.useState({});
    const [feast,setFeast] = React.useState({});
    const [poll,setPoll] = React.useState({});
    const [name,setPollName] = React.useState("");

    function handleDeepLink(event){
        let data = Linking.parse(event.url).queryParams;
        setData(data);
        setpage(2);
    }

    React.useEffect(() => {

        async function getInitialURL(){
            const initialUrl = await Linking.getInitialURL();
            if(initialUrl){
                let data = Linking.parse(initialUrl).queryParams;
                setData(data);
                if(Object.keys(data).length > 0)
                {
                    setpage(2);
                }
            }
        }

        Linking.addEventListener("url",handleDeepLink);

        if(!data)
        {
            getInitialURL();
        }

        registerForPushNotificationsAsync().then(token => setExpoPushToken(token));

        // This listener is fired whenever a notification is received while the app is foregrounded
        notificationListener.current = Notifications.addNotificationReceivedListener(notification => {
            setNotification(notification);
        });

        // This listener is fired whenever a user taps on or interacts with a notification (works when app is foregrounded, backgrounded, or killed)
        responseListener.current = Notifications.addNotificationResponseReceivedListener(response => {

        });

        return () => {
            Linking.removeEventListener("url");
            Notifications.removeNotificationSubscription(notificationListener.current);
            Notifications.removeNotificationSubscription(responseListener.current);
        };
    }, []);

    const pagging = (page) => {

        switch (page) {
            case 0:
                return (<Signin pagging={setpage} signin={signin} token={expoPushToken} />);
            case 1:
                return (<UserDash pagging={setpage} user={user} />);
            case 2:
                return (<Recover pagging={setpage}  signin={signin} token={forgot} expotoken={expoPushToken} data={data}/>);
            case 3:
                return (<Signup pagging={setpage} />);
            case 4:
                return (<Forgot pagging={setpage} />);
            case 5:
                return (<Settings pagging={setpage} user={user} />);
            case 6:
                return (<CreateRestaurant pagging={setpage} user={user} />);
            case 7:
                return (<CreatePoll pagging={setpage} user={user} />);
            case 8:
                return (<CreateFeastGroup pagging={setpage} user={user} />);
            case 9:
                return (<CreateRestaurantGroup pagging={setpage} user={user} />);
            case 10:
                return (<ManageRestaurantGroup pagging={setpage} user={user} setRestaurant={setRestaurant}/>);
            case 11:
                return (<UpdateRestaurantGroup pagging={setpage} user={user} restaurant={restaurant} />);
            case 12:
                return (<ManageFeastGroup pagging={setpage} user={user} setFeast={setFeast}/>);
            case 13:
                return (<UpdateFeastGroup pagging={setpage} user={user} feast={feast} />);
            case 14:
                return (<ManagePolls pagging={setpage} user={user} setPoll={setPoll}/>);
            case 15:
                return (<UpdatePoll pagging={setpage} user={user} poll={poll} />);    
            case 16:
                return (<ManageVotes pagging={setpage} user={user} setPoll={setPoll}/>);
            case 17:
                return (<Vote pagging={setpage} user={user} poll={poll} />);
            case 18:
                return (<ManageRestaurants pagging={setpage} user={user} setRestaurant={setRestaurant}/>);
            case 19:
                return (<UpdateRestaurant pagging={setpage} user={user} restaurant={restaurant} />);
            case 20:
                return (<Results pagging={setpage} user={user} setPollName={setPollName} setPoll={setPoll}/>);
            case 21:
                return (<VoteResults pagging={setpage} user={user} name={name} poll={poll}/>);
            default:
                return (<Signin pagging={setpage} signin={signin} token={expoPushToken} />);
        }

    }


    return (
        <ToastProvider>
            <NavigationContainer fallback={<Text>Loading...</Text>}>
                <SafeAreaView>
                    {pagging(page)}
                </SafeAreaView>
            </NavigationContainer>
        </ToastProvider>
    );
}

async function registerForPushNotificationsAsync() {
    let token;
    if (Platform.OS === 'android' || Platform.OS === 'ios') {
        if (Constants.isDevice) {
            const { status: existingStatus } = await Notifications.getPermissionsAsync();
            let finalStatus = existingStatus;
            if (existingStatus !== 'granted') {
                const { status } = await Notifications.requestPermissionsAsync();
                finalStatus = status;
            }
            if (finalStatus !== 'granted') {
                alert('Failed to get push token for push notification!');
                return;
            }
            token = (await Notifications.getExpoPushTokenAsync()).data;
        } else {
            alert('Must use physical device for Push Notifications');
        }
    }

    if (Platform.OS === 'android') {
        Notifications.setNotificationChannelAsync('default', {
            name: 'default',
            importance: Notifications.AndroidImportance.MAX,
            vibrationPattern: [0, 250, 250, 250],
            lightColor: '#FF231F7C',
        });
    }

    return token;
}