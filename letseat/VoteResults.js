import React, {useEffect} from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { ActivityIndicator, KeyboardAvoidingView, BackHandler, SafeAreaView, StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Platform } from "react-native";
import { useToast } from "react-native-toast-notifications";
import Button from './button';
import './global.js';

export default function VoteResults(props) {
    useFocusEffect(
        React.useCallback(() => {
            const onBackPress = () => {
                goback();
                return true;
            };

            BackHandler.addEventListener(
                'hardwareBackPress', onBackPress
            );

            return () =>
                BackHandler.removeEventListener(
                    'hardwareBackPress', onBackPress
                );
        }, [])
    );



    useEffect(() => {
        const requestOptions = {
            method: "GET",
            headers: {
                "LETSEAT-AUTHKEY": props.user.user_token,
                "Content-Type": "application/json"
            },
        };
        fetch(global.server + "vote/read.php?poll_id=" + props.poll,requestOptions).then(res => res.json()).then(result => {
            let results = calculateWinners(result);
            setFeastGroups(results);
            // console.log("Hex: " + JSON.stringify(results));
            return results;
        }).then((res) => {
            fetch(global.server + "restaurant/read.php?id=" + res[0][0],requestOptions).then(res => res.json()).then(result => {
                // console.log("here" + JSON.stringify(res[0][0]));
                setWinner(result)
                setWinnerTwo(result)
                // let results = calculateWinners(result);      
            }).then(() => {
                setResultsAreIn(res);
                setLoading(false)
            })
        })
    },[]);
    const [winner,setWinner] = React.useState({});
    const [winnerTwo,setWinnerTwo] = React.useState({});
    const orderArr = arr => {
        arr.sort((a,b) => a[1] < b[1]);
        return arr;
    }

    const [resultsAreIn,setResultsAreIn] = React.useState([]);

    const calculateWinners = array => {
        // get unique polls
        const uniquePoll = [...new Set(array.map(item => item.poll_id))];
        // console.log(uniquePoll);
        
        // get votes for each poll -> [poll-1[],poll-2[],poll-n[]]
        const votes = uniquePoll.map(poll => array.map(vote => vote.poll_id===poll?vote:null).filter(vote => vote!==null));
        // console.log(votes);

        // get vote candidate tallies from votes
        const tallies = votes.map(poll => poll.map(vote => vote.candidate_id))
        // console.log(tallies);

        // combine vote candidate tallies when numbers are same
        const fin = tallies.map(poll => countDups(poll))[0];
        return orderArr(Object.entries(fin));
    }

    const countDups = array => {
        const counts = {};
        array.forEach(function (x) { counts[x.toString()] = (counts[x.toString()] || 0) + 1; });
        // console.log(counts);
        return counts;
    }
    

    const styles = StyleSheet.create(
        Platform.select({
            ios: global.mainstyles,
            android: global.mainstyles,
            default: global.mainstyles
        })
    );


    const [Feasts,setFeasts] = React.useState([]);
    const [FeastGroups,setFeastGroups] = React.useState([]);
    const [initialLoading,setLoading] = React.useState(true);



    const goback = () => {
        props.pagging(20)
    }


    return (
        <SafeAreaView>
            <View style={styles.main}>
                <View style={styles.navbar}>
                    <View style={styles.backbuttonview}>
                        <Button
                            textstyle={styles.backbutton}
                            title="Back"
                            onPress={goback}>
                        </Button>
                    </View>
                </View>
                <View style={styles.scrollView}>
                    <KeyboardAvoidingView
                        behavior={Platform.OS === "ios" ? "height" : "padding"}
                        enabled>
                        <ScrollView>
                            <View
                                style={styles.signin}
                            >
                                <Text style={styles.dashboardheader}>
                                    Poll Results: {props.name}
                                </Text>
                                <Text style={styles.dashboardheader}>
                                    Winner: {winner.name}
                                    {/* + "|" + JSON.stringify(winnerTwo)} */}
                                </Text>
                                {
                                    // initialLoading?
                                    // <View style={styles.topbutton}>
                                    //     <ActivityIndicator color={styles.topbutton.color} size="large" />
                                    // </View>:
                                    // resultsAreIn.map((group,id) => {
                                    //     return (<Text>{JSON.stringify(group)}</Text>)
                                    // })
                                }
                            </View>
                        </ScrollView>
                    </KeyboardAvoidingView>
                </View>
            </View>
        </SafeAreaView>

    )
}