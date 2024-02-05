import React, { useEffect }  from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { ActivityIndicator, KeyboardAvoidingView, BackHandler, SafeAreaView, StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Platform } from "react-native";
import Button from './button';
import './global.js';

export default function Vote(props) {
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

    const [time, setTime] = React.useState(props.initialValue || 15);
    const timerRef = React.useRef(time);

    useEffect(() => {
        const requestOptions = {
            method: "GET",
            headers: {
                "LETSEAT-AUTHKEY": props.user.user_token,
                "Content-Type": "application/json"
            },
        };
          
        fetch(global.server + "restaurant/read.php?all=1",requestOptions).then(res => res.json()).then(result => {
            setPolls(result);
            return result
        }).then(Pollsx=>{
            fetch(global.server + "candidate/read.php?poll_id=" + props.poll,requestOptions).then(res => res.json()).then(result => {
                console.log(props.poll);
                let hold = Pollsx.map(rest => result.includes(rest.id)?{...rest,selected:true}:{...rest,selected:false});
                setGroupPolls(hold)
            });
        })
        const timerId = setInterval(() => {
            timerRef.current -= 1;
            if (timerRef.current < 0) {
                clearInterval(timerId);
                selectThisOne(0);
            } else {
                setTime(timerRef.current);
            }
        }, 1000);
        return () => {clearInterval(timerId);};
    },[]);

    const styles = StyleSheet.create(
        Platform.select({
            ios: global.mainstyles,
            android: global.mainstyles,
            default: global.mainstyles
        })
    );


    const [Polls,setPolls] = React.useState([]);
    const [PollGroups,setGroupPolls] = React.useState([]);




    const [isLoadingName, setLoadingName] = React.useState(false);

    const notesRef = React.useRef(null);


    const selectThisOne = (id) => {
        setLoadingName(true);
        const requestOptions = {
            method: "POST",
            headers: {
                "LETSEAT-AUTHKEY": props.user.user_token,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                poll_id:props.poll,
                vote_id:id
            }),
        };
        console.log(requestOptions.body)

        fetch(global.server + "vote/create.php", requestOptions).then(res => res.json()).then(result => {
            // console.log(result);
            setLoadingName(false);
            if (result.result) {
                alert("Thanks For Voting!");
                goback();
            } else {
                alert("Vote Failed");
                goback();
            }
        }).catch(() => {
            setTimeout(() => {
                setLoadingName(false);
                alert("Failed To Connect To Server");
            }, 2000);
        }).finally(() => {
            setTimeout(() => setLoadingName(false), 2000);
        });
    }
    const goback = () => {
        props.pagging(16)
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
                                    Vote
                                </Text>
                                <Text>Ballot: {time}</Text>
                                {
                                    PollGroups.map(group => {
                                        return (group.selected?<Button 
                                            textstyle={styles.topbutton}
                                            title={group.name}
                                            onPress={() => {
                                                selectThisOne(group.id);
                                            }}
                                            accessibilityLabel={group.name}
                                            isLoading={isLoadingName}>
                                        </Button>:null)
                                    })
                                }
                                <Button 
                                    textstyle={styles.topbutton}
                                    title="None"
                                    onPress={() => {
                                        selectThisOne(0);
                                    }}
                                    accessibilityLabel="None"
                                    isLoading={isLoadingName}>
                                </Button>
                            </View>
                        </ScrollView>
                    </KeyboardAvoidingView>
                </View>
            </View>
        </SafeAreaView>

    )
}